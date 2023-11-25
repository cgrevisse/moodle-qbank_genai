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
 * Callback implementations for Generative AI Question Bank
 *
 * @package    qbank_genai
 * @copyright  2023 Christian Grévisse <christian.grevisse@uni.lu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Defines the necessary capabilities for this plugin.
 *
 * @return array The necessary capabilities.
 */
function required_capabilities() {
    return ['moodle/question:add', 'moodle/course:viewhiddenactivities'];
}

/**
 * Insert a link to the secondary navigation of a course.
 *
 * @param navigation_node $navigation The settings navigation object
 * @param stdClass $course The course
 * @param context $context Course context
 */
function qbank_genai_extend_navigation_course(navigation_node $navigation, stdClass $course, context $context) {
    if (!isloggedin() || isguestuser() || !has_all_capabilities(required_capabilities(), $context)) {
        return;
    }

    $navigation->add(
        get_string('title', 'qbank_genai'),
        new moodle_url('/question/bank/genai/index.php', ['courseid' => $course->id]),
        navigation_node::COURSE_INDEX_PAGE,
    );
}
