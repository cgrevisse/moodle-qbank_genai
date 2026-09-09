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

namespace qbank_genai\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/bank/genai/lib.php');
require_once($CFG->dirroot . '/question/bank/genai/vendor/autoload.php');

/**
 * Class generate_questions
 *
 * @package    qbank_genai
 * @copyright  2026 Christian Grévisse <christian.grevisse@uni.lu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generate_questions extends external_api {
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'contextID' => new external_value(PARAM_INT, 'Context ID'),
            'courseID' => new external_value(PARAM_INT, 'Course ID'),
            'fileID' => new external_value(PARAM_INT, 'File ID'),
            'numberMCQs' => new external_value(PARAM_INT, 'Number of MCQs'),
            'numberEssays' => new external_value(PARAM_INT, 'Number of essays'),
        ]);
    }

    /**
     * Returns description of method result value
     * @return external_value
     */
    public static function execute_returns() {
        return new external_value(PARAM_TEXT, 'Result of the generation');
    }

    /**
     * Generate questions.
     *
     * The selected file is uploaded to OpenAI, whereupon questions are generated based on its content.
     * Questions are then programmatically added to a newly created question bank category.
     *
     * @param int $contextid The ID of the context
     * @param int $courseid The ID of the course
     * @param int $fileid The ID of the file
     * @param int $numbermcqs Number of multiple choice questions to be generated
     * @param int $numberessays Number of essay questions to be generated
     * @return string The result
     */
    public static function execute($contextid, $courseid, $fileid, $numbermcqs, $numberessays) {
        $params = self::validate_parameters(
            self::execute_parameters(),
            ['contextID' => $contextid, 'courseID' => $courseid, 'fileID' => $fileid,
                'numberMCQs' => $numbermcqs, 'numberEssays' => $numberessays]
        );

        $context = \context_course::instance($courseid);
        self::validate_context($context);
        require_all_capabilities(qbank_genai_required_capabilities(), $context);

        // Get OpenAI API key from plugin settings.
        $openaiapikey = qbank_genai_get_openai_apikey($courseid);
        if (empty($openaiapikey)) {
            throw new \Exception(get_string('noopenaiapikey', 'qbank_genai'));
        }

        // Initialize OpenAI client.
        $client = \OpenAI::factory()
            ->withApiKey($openaiapikey)
            ->withHttpClient(new \GuzzleHttp\Client(['timeout' => 600]))
            ->make();

        // Get file.
        $fileinfo = qbank_genai_get_fileinfo_for_resource($fileid);

        // Upload files: Copy necessary as Moodle renames files upon upload and OpenAI requires
        // a file extension (and symbolic link would still take original name).
        $tempfolder = make_temp_directory('qbank_genai');
        $copypath = $tempfolder . "/" . basename($fileinfo->path) . "." . $fileinfo->extension;
        $fileinfo->file->copy_content_to($copypath);

        $response = $client->files()->upload([
            'purpose' => 'user_data',
            'file' => fopen($copypath, 'r'),
        ]);
        $uploadedfileid = $response->id;

        unlink($copypath);

        $systemprompt = "You are a quiz generator. You will be provided a file about which you should create ";
        $systemprompt .= "an indicated number of multiple choice questions (MCQ) respectively essay questions, ";
        $systemprompt .= "in the same language as its content. Each multiple choice question shall have between ";
        $systemprompt .= "3 and 5 answers and only 1 correct answer. Make sure all distractors are plausible and ";
        $systemprompt .= "that the correct answer is not much longer than any distractor. For essay questions, ";
        $systemprompt .= "also indicate grading information (a rubric with grading criteria and points assigned ";
        $systemprompt .= "to each criterion, each on a separate line) that can be used for automatic grading, ";
        $systemprompt .= "as well as the maximum number of points.";

        $userprompt = 'Create ' . $numbermcqs . ' multiple choice questions and ' . $numberessays;
        $userprompt .= ' essay questions on the content of the provided file.';

        // Call OpenAI to generate questions.
        $response = $client->responses()->create([
            'model' => 'gpt-6-astra',
            'input' => [
                [
                    'role' => 'system',
                    'content' => $systemprompt,
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'input_file',
                            'file_id' => $uploadedfileid,
                        ],
                        [
                            'type' => 'input_text',
                            'text' => $userprompt,
                        ],
                    ],
                ],
            ],
            'text' => [
                "format" => [
                    'type' => 'json_schema',
                    'name' => 'question_response',
                    'strict' => true,
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'mcq' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'stem' => [
                                            'type' => 'string',
                                        ],
                                        'answers' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'text' => [
                                                        'type' => 'string',
                                                    ],
                                                    'correct' => [
                                                        'type' => 'boolean',
                                                    ],
                                                ],
                                                'required' => ['text', 'correct'],
                                                'additionalProperties' => false,
                                            ],
                                        ],
                                    ],
                                    'required' => ['stem', 'answers'],
                                    'additionalProperties' => false,
                                ],
                            ],
                            'essay' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'stem' => [
                                            'type' => 'string',
                                        ],
                                        'maxpoints' => [
                                            'type' => 'number',
                                        ],
                                        'graderinfo' => [
                                            'type' => 'string',
                                        ],
                                    ],
                                    'required' => ['stem', 'maxpoints', 'graderinfo'],
                                    'additionalProperties' => false,
                                ],
                            ],
                        ],
                        'required' => ['mcq', 'essay'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
        ]);

        $client->files()->delete($uploadedfileid);

        $questiondata = [];

        // Parse the response to get the tags.
        try {
            $questiondata = json_decode($response->outputText);
        } catch (\Exception $e) {
            throw new \Exception(get_string('questiongenerationparsingerror', 'qbank_genai'));
        }

        $multiplechoicequestions = $questiondata->mcq;
        $essayquestions = $questiondata->essay;

        if (count($multiplechoicequestions) + count($essayquestions) > 0) {
            // Create question bank category and questions.
            $category = qbank_genai_create_question_category($contextid, get_coursemodule_from_id("resource", $fileid)->name);

            $i = 0;

            foreach ($multiplechoicequestions as $data) {
                $question = new \stdClass();
                $question->stem = $data->stem;
                $question->answers = [];

                foreach ($data->answers as $answer) {
                    $question->answers[] = (object) ["text" => $answer->text, "weight" => $answer->correct ? 1.0 : 0.0];
                }

                $questionname = str_pad(strval(++$i), 3, "0", STR_PAD_LEFT);

                qbank_genai_create_mcq($questionname, $question, $category);
            }

            foreach ($essayquestions as $data) {
                $questionname = str_pad(strval(++$i), 3, "0", STR_PAD_LEFT);
                qbank_genai_create_essay($questionname, $data->stem, $data->maxpoints, nl2br($data->graderinfo), $category);
            }

            return get_string(
                'questiongenerationsuccess',
                'qbank_genai',
                ['number' => count($multiplechoicequestions) + count($essayquestions), 'category' => $category->name]
            );
        } else {
            throw new \Exception(get_string('noquestionsgenerated', 'qbank_genai'));
        }
    }
}
