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

namespace qbank_genai\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use moodleform;

/**
 * Class settings_form
 *
 * @package    qbank_genai
 * @copyright  2024 Christian Grévisse <christian.grevisse@uni.lu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings_form extends moodleform {
    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'openaiapisettings', get_string('openaiapisettings', 'qbank_genai'));

        $mform->addElement('passwordunmask', 'openaiapikey', get_string('openaiapikey', 'qbank_genai'));
        $mform->setType('openaiapikey', PARAM_ALPHANUMEXT);
        $mform->addHelpButton('openaiapikey', 'openaiapikey:course', 'qbank_genai');

        $this->add_action_buttons();
    }

    /**
     * Extra validation: Nothing for the moment.
     *
     * @param array $data Submitted data
     * @param array $files Not used here
     * @return array element name/error description pairs (if any)
     */
    public function validation($data, $files) {
        return [];
    }
}
