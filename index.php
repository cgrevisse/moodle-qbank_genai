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

$url = new moodle_url('/question/bank/genai/index.php');
$PAGE->set_url($url);

$courseid = required_param('courseid', PARAM_INT);
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


require_once($CFG->dirroot . '/question/bank/genai/vendor/autoload.php');

$parser = new \Smalot\PdfParser\Parser();

raise_memory_limit(MEMORY_HUGE);

$pdf = $parser->parseFile($path);
$text = $pdf->getText();

echo html_writer::tag('blockquote', $text);


$openaiapikey = get_config('qbank_genai', 'openaiapikey');
if (!empty($openaiapikey)) {
    echo "OpenAI API key: '$openaiapikey'";
}

echo $OUTPUT->footer();
