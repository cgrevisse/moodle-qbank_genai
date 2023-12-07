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

require_once($CFG->dirroot. '/question/bank/genai/lib.php');
require_once($CFG->dirroot. '/question/bank/genai/classes/handler/handler.php');
require_once($CFG->dirroot . '/question/bank/genai/vendor/autoload.php');

use qbank_genai\handler\HandlerRegistry;
use stdClass;

/**
 * Class generation_task
 *
 * @package    qbank_genai
 * @copyright  2023 Christian Grévisse <christian.grevisse@uni.lu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generation_task extends \core\task\adhoc_task {

    public static function instance(array $resources, int $userid, int $contextid) {
        $task = new self();
        $task->set_custom_data((object) [
            'resources' => $resources,
            'contextid' => $contextid,
        ]);
        $task->set_userid($userid);

        return $task;
    }

    public function execute() {
        $openaiapikey = get_config('qbank_genai', 'openaiapikey');
        if (empty($openaiapikey)) {
            throw new \Exception('No OpenAI API key provided.');
        }

        $data = $this->get_custom_data();

        $category = create_question_category($data->contextid, get_resource_names_string($data->resources));

        foreach ($data->resources as $resource) {
            // 1. Extract text from PDF
            $file = get_fileinfo_for_resource($resource->id);
        
            $handler = HandlerRegistry::get_registry()->get_handler($file->extension);
        
            if ($handler != null) {
                //$text = $handler->extract_text($file);

                //mtrace($resource->name);
                //mtrace($text);

                // TODO: 2. LLM
                //generate_questions($text, $openaiapikey);

                // TODO: 3. Create question bank category and questions
                /*
                $category = create_question_category($context->id, 'Test');

                $question = (object) [
                    "stem" => "What is the capital of France?",
                    "answers" => [
                        (object) ["text" => "Paris", "weight" => 1.0],
                        (object) ["text" => "Strasbourg", "weight" => 0.0],
                        (object) ["text" => "Lyon", "weight" => 0.0],
                        (object) ["text" => "Marseille", "weight" => 0.0],
                    ],
                ];

                create_question("Q001", $question, $category);
                */
                
            }
        }

        //mtrace(var_export());
    }

}
