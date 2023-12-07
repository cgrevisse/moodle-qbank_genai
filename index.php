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

use qbank_genai\task\generation_task;

require('../../../config.php');
require_once($CFG->dirroot. '/question/bank/genai/lib.php');

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

// Check for OpenAI API key
$openaiapikey = get_config('qbank_genai', 'openaiapikey');
if (empty($openaiapikey)) {
    echo html_writer::tag('div', get_string('noopenaiapikey', 'qbank_genai'), ['class' => 'alert alert-warning']);
}

// Show ongoing generation tasks (if any)
$existingtasks = $DB->get_records('task_adhoc', ['userid' => $USER->id, 'component' => 'qbank_genai']);
if (!empty($existingtasks)) {
    
    echo html_writer::start_tag('div', ['class' => 'alert alert-info']);
    echo html_writer::tag('p', get_string('ongoingtasks', 'qbank_genai'));
    echo html_writer::start_tag('ul');

    foreach ($existingtasks as $task) {
        echo html_writer::start_tag('li');
        echo html_writer::tag('span', format_text(get_resource_names_string(json_decode($task->customdata)->resources), FORMAT_PLAIN));
        echo html_writer::tag('small', userdate($task->timecreated), ['class' => 'text-muted ml-2']);
    }
    
    echo html_writer::end_tag('li');
    echo html_writer::end_tag('ul');
    echo html_writer::end_tag('div');

}

// Get course resources
$resources = get_course_resources($course);

// Form handling
$mform = new \qbank_genai\form\generation_form($url, $resources);

if ($fromform = $mform->get_data()) {
    $ids = [];

    foreach ($fromform->resource as $id => $selected) {
        if (boolval($selected)) {
            $ids[] = $id;
        }
    }

    $selected_resources = [];
    
    foreach ($resources as $resource) {
        if (in_array($resource->id, $ids)) {
            $selected_resources[] = (object) [
                "id" => $resource->id,
                "name" => $resource->name,
                "visible" => $resource->visible,
            ];
        }
    }
    
    // Launch generation task
    $task = \qbank_genai\task\generation_task::instance($selected_resources, $USER->id, $context->id);
    \core\task\manager::queue_adhoc_task($task); // add true to avoid duplicates

    // Log generation task launched
    $event = \qbank_genai\event\generation_launched::create(['context' => $context, 'other' => ["ids" => $ids]]);
    $event->trigger();

    // Redirect to this page again (seems to interfere with mtrace in adhoc task ...). Also, consider redirecting before any $OUTPUT->...
    redirect($PAGE->url);
} else {
    $mform->display();
}

echo $OUTPUT->footer();
