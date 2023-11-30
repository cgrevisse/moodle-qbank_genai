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

/**
 * TODO describe file index
 *
 * @package    qbank_genai
 * @copyright  2023 Christian Grévisse <christian.grevisse@uni.lu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');
require_once($CFG->dirroot. '/question/bank/genai/lib.php');

require_once($CFG->dirroot . '/question/bank/genai/vendor/autoload.php');

$courseid = required_param('courseid', PARAM_INT);

$url = new moodle_url('/question/bank/genai/index.php', ['courseid' => $courseid]);
$PAGE->set_url($url);

$course = get_course($courseid);

require_login($course);

$course = course_get_format($course)->get_course();

$context = context_course::instance($course->id);
$PAGE->set_context($context);

require_all_capabilities(required_capabilities(), $context);

$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('title', 'qbank_genai'));

$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('title', 'qbank_genai'));

/*
// Get all resources from course

$info = get_fast_modinfo($course);
$resources = $info->instances['resource'];

$fs = get_file_storage();

foreach ($resources as $resource) {
    $cmid = context_module::instance($resource->id)->id;
    $files = $fs->get_area_files($cmid, 'mod_resource', 'content', 0, 'filename', false);
    $file = reset($files);
    $path = $fs->get_file_system()->get_remote_path_from_storedfile($file);

    if ($file) {
        echo "$resource->name | Visible: $resource->visible | File name: {$file->get_filename()} | File path: $path<hr>";
    }
}
*/

/*
// Extract text from PDF

$parser = new \Smalot\PdfParser\Parser();

raise_memory_limit(MEMORY_HUGE);

$pdf = $parser->parseFile($path);
$text = $pdf->getText();

echo html_writer::tag('blockquote', $text);
*/

/*
// Get OpenAI API key from plugin settings

$openaiapikey = get_config('qbank_genai', 'openaiapikey');
if (!empty($openaiapikey)) {
    //echo "OpenAI API key: '$openaiapikey'";
}
*/

/*
// Test MCQ data

$mcq->id = 0;
$mcq->category = 0;
$mcq->idnumber = null;
$mcq->contextid = 0;
$mcq->parent = 0;

$mcq->questiontextformat = FORMAT_HTML;
$mcq->generalfeedbackformat = FORMAT_HTML;
$mcq->defaultmark = 1;
$mcq->penalty = 0;
$mcq->length = 1;
$mcq->stamp = make_unique_id_code();
$mcq->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
$mcq->version = 1;
$mcq->timecreated = time();
$mcq->timemodified = time();
$mcq->createdby = $USER->id;
$mcq->modifiedby = $USER->id;

$mcq->name = 'GenAI-001';
$mcq->questiontext = 'What is the capital of France?';
$mcq->generalfeedback = '';
$mcq->qtype = "qtype_multichoice"; // question_bank::get_qtype('multichoice');

$mcq->shuffleanswers = 1;
$mcq->answernumbering = 'abc';
$mcq->showstandardinstruction = 0;

$mcq->answers = [
    0 => new question_answer(0, 'Paris', 1, '', FORMAT_HTML),
    1 => new question_answer(1, 'Marseille', 0, '', FORMAT_HTML),
    2 => new question_answer(2, 'Strasbourg', 0, '', FORMAT_HTML),
    3 => new question_answer(3, 'Lyon', 0, '', FORMAT_HTML),
];
*/

/*
// Programmatic way of creating question category and adding MCQ

require_once($CFG->dirroot. '/question/bank/genai/helper.php');

$questiondata = get_multichoice_question_data();
$formdata = get_multichoice_question_form_data();

$category = create_question_category($context->id);

$formdata->category = "{$category->id},{$category->contextid}";


require_once($CFG->dirroot. '/question/type/edit_question_form.php');
require_once($CFG->dirroot. '/question/type/multichoice/edit_multichoice_form.php');
qtype_multichoice_edit_form::mock_submit((array)$formdata);

$form = get_question_editing_form($category, $questiondata);

assert($form->is_validated());

$fromform = $form->get_data();

$qtype = new qtype_multichoice();
$qtype->save_question($questiondata, $fromform);
*/

/*
// Task API for asynchronous jobs

$task = \qbank_genai\task\generation_task::instance(42, "Dale!", $USER->id);
\core\task\manager::queue_adhoc_task($task); // add true to avoid duplicates

$existingtasks = $DB->get_records('task_adhoc', ['userid' => $USER->id, 'component' => 'qbank_genai']);
print_object($existingtasks);
*/

/*
// Event API

$event = \qbank_genai\event\generation_launched::create(['context' => $context, 'other' => ["foo" => 42]]);
$event->trigger();
*/

/*
// Forms API

require_once($CFG->dirroot. '/question/bank/genai/classes/form/generation_form.php');

$dummydata = [
    ["id" => 42, "name" => "Introduction", "visible" => true, "path" => "Introduction.pdf"],
    ["id" => 16, "name" => "Processes", "visible" => false, "path" => "Processes.pdf"],
];

$mform = new \qbank_genai\form\generation_form($url, $dummydata);

if ($fromform = $mform->get_data()) {
    // When the form is submitted, and the data is successfully validated, the `get_data()` function will return the data posted in the form.

    var_dump($fromform);

    //redirect($PAGE->url);
} else {
    // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed or on the first display of the form.
   
    // Set default data (if any).
    //$mform->set_data(["email" => ""]);
   
    $mform->display();
}
*/

/*
// Basic usage of OpenAI API Client (https://github.com/openai-php/client)

$client = OpenAI::client($openaiapikey);

$result = $client->chat()->create([
    'model' => 'gpt-4',
    'messages' => [
        ['role' => 'user', 'content' => 'What is the Collège de France?'],
    ],
]);

echo $result->choices[0]->message->content;
*/

echo $OUTPUT->footer();
