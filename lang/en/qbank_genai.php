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
$string['assistantid_help'] = 'ID concerning the <a href="https://platform.openai.com/docs/assistants/overview" target="_blank">Assistants API</a> of OpenAI. This will be set by the plugin.';

$string['errormsg_noneselected'] = 'Please select at least one resource.';

$string['noopenaiapikey'] = 'You need to set an OpenAI API key.';
$string['noresources'] = 'There are no resources in your course.';

$string['ongoingtasks'] = 'The following generation tasks are ongoing:';

$string['openaiapikey'] = 'OpenAI API key';
$string['openaiapikey_help'] = 'To be created at <a href="https://platform.openai.com/api-keys" target="_blank">https://platform.openai.com/api-keys</a>.';
$string['openaiapisettings'] = 'OpenAI API Settings';

$string['pluginname'] = 'Generative AI Question Bank';

$string['privacy:metadata:qbank_genai_openai_settings:userid'] = 'Table that stores data related to the OpenAI API';
$string['privacy:metadata:qbank_genai_openai_settings'] = 'The user\'s ID';


$string['settings'] = 'Generative AI Question Bank Settings';
$string['title'] = 'Generate questions';
