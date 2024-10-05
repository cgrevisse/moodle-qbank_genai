<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace qbank_genai\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/bank/genai/lib.php');
require_once($CFG->dirroot.'/question/bank/genai/vendor/autoload.php');

/**
 * Class generation_task
 *
 * @package    qbank_genai
 * @copyright  2023 Christian Grévisse <christian.grevisse@uni.lu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generation_task extends \core\task\adhoc_task {

    /**
     * Factory method for this class
     *
     * @param array $resources The selected resources for which questions shall be generated
     * @param int $userid The user who started the task
     * @param int $contextid The context ID (needed for the question bank category)
     *
     * @return static the singleton instance
     */
    public static function instance(array $resources, int $userid, int $contextid) {
        $task = new self();
        $task->set_custom_data((object) [
            'resources' => $resources,
            'contextid' => $contextid,
        ]);
        $task->set_userid($userid);

        return $task;
    }

    /**
     * This method implements the question generation by 1) extracting text from the selected resources; 2) generating
     * questions via an LLM; and 3) programmatically add the questions in a newly created question bank category.
     */
    public function execute() {
        $openaiapikey = get_config('qbank_genai', 'openaiapikey');
        if (empty($openaiapikey)) {
            throw new \Exception('No OpenAI API key provided.');
        }

        $assistantid = get_config('qbank_genai', 'assistantid');
        if (empty($assistantid)) {
            $assistantid = qbank_genai_create_openai_assistant($openaiapikey);
            set_config("assistantid", $assistantid, "qbank_genai");
        }

        $client = \OpenAI::client($openaiapikey);

        $data = $this->get_custom_data();

        $category = qbank_genai_create_question_category($data->contextid, qbank_genai_get_resource_names_string($data->resources));
        mtrace("Category created: ".$category->name);

        foreach ($data->resources as $resource) {
            $file = qbank_genai_get_fileinfo_for_resource($resource->id);

            mtrace("Uploading file:");
            mtrace($file->path);

            // Upload files: Copy necessary as Moodle renames files upon upload and OpenAI requires
            // a file extension (and symbolic link would still take original name).
            $copypath = $file->path.".".$file->extension;
            $file->file->copy_content_to($copypath);

            $response = $client->files()->upload([
                'purpose' => 'assistants',
                'file' => fopen($copypath, 'r'),
            ]);

            unlink($copypath);

            $fileid = $response->id;

            mtrace("File uploaded: $fileid");

            // Create vector store.
            $response = $client->vectorStores()->create([
                'name' => 'Moodle GenAI Bank Plugin Vector Store', 'file_ids' => [$fileid]]);
            $vectorstoreid = $response->id;

            mtrace("Vector store created: $vectorstoreid");

            // Create a Thread.
            $response = $client->threads()->create([
                'tool_resources' => ['file_search' => ['vector_store_ids' => [$vectorstoreid]]]]);
            $threadid = $response->id;

            mtrace("Thread created: $threadid");

            // Add a Message to a Thread.
            $message = 'Create 10 multiple choice questions for the provided file. ';
            $message .= 'Each question shall have 4 answers and only 1 correct answer. ';
            $message .= 'The output shall be in JSON format, i.e., an array of objects where each object contains the stem, ';
            $message .= 'an array for the answers and the index of the correct answer. Name the keys "stem", "answers", ';
            $message .= '"correctAnswerIndex". The output shall only contain the JSON, nothing else.';

            $response = $client->threads()->messages()->create($threadid, [
                'role' => 'user',
                'content' => $message,
            ]);
            $messageid = $response->id;

            mtrace("Message created: $messageid");

            // Run the Assistant.
            $response = $client->threads()->runs()->create($threadid, ['assistant_id' => $assistantid]);
            $runid = $response->id;

            mtrace("Run created: $runid");

            // Poll for status -> TODO: Streaming?
            do {
                sleep(1);
                $response = $client->threads()->runs()->retrieve($threadid, $runid);
                $status = $response->status;
            } while ($status != 'completed' && $status != 'failed');

            if ($status == 'failed') {
                throw new \Exception('Error during run, check task logs for further details.');
                mtrace("Error during run:");
                mtrace(var_export($response->lastError->toArray()));
            }

            // Completed: Get the Assistant's Response.
            mtrace("Run completed!");

            // Create question bank category and questions.
            $response = $client->threads()->messages()->list($threadid, ['limit' => 1]);
            $questiondata = json_decode(trim($response->data[0]->content[0]->text->value, '`json'));

            mtrace(var_export($response->data));

            $i = 0;

            foreach ($questiondata as $data) {
                mtrace(var_export($data));

                $question = new \stdClass();
                $question->stem = $data->stem;
                $question->answers = [];

                foreach ($data->answers as $answer) {
                    $question->answers[] = (object) ["text" => $answer, "weight" => 0.0];
                }

                $question->answers[$data->correctAnswerIndex]->weight = 1.0;

                $questionname = str_pad(strval(++$i), 3, "0", STR_PAD_LEFT);

                qbank_genai_create_question($questionname, $question, $category);

                mtrace("Question created: $questionname");
            }

            // Delete vector store.
            $response = $client->vectorStores()->delete($vectorstoreid);
            mtrace("Vector store deleted!");

            // Delete the file.
            $response = $client->files()->delete($fileid);
            mtrace("File deleted!");
        }
    }
}
