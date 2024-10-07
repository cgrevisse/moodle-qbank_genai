<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     qbank_genai
 * @category    string
 * @copyright   2023 Christian Grévisse <christian.grevisse@uni.lu>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['assistantid'] = 'Assistant ID';
$string['assistantiddesc'] = 'ID concerning the <a href="https://platform.openai.com/docs/assistants/overview" target="_blank">Assistants API</a> of OpenAI. This will be set by the plugin.';

$string['errormsg_noneselected'] = 'Please select at least one resource.';

$string['noopenaiapikey'] = 'You have not set an OpenAI API key so far. Please refer to the plugin settings.';
$string['noresources'] = 'There are no resources in your course.';

$string['ongoingtasks'] = 'The following generation tasks are ongoing:';

$string['openaiapikey'] = 'OpenAI API key';
$string['openaiapikeydesc'] = 'To be created at <a href="https://platform.openai.com/api-keys" target="_blank">https://platform.openai.com/api-keys</a>.';

$string['pluginname'] = 'Generative AI Question Bank';

$string['privacy:metadata'] = 'The GenAI question bank plugin does not store any personal data.';

$string['title'] = 'Generate questions';
