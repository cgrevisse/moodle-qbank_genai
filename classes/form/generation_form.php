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

require_once($CFG->libdir.'/formslib.php');

use moodleform;

/**
 * Class generation_form
 *
 * @package    qbank_genai
 * @copyright  2023 Christian Grévisse <christian.grevisse@uni.lu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generation_form extends moodleform {

    /**
     * Form definition: The user can select the resources for which questions shall be generated.
     */
    public function definition() {
        $mform = $this->_form;

        // Add a checkbox controller for all checkboxes in `group => 1`: (necessary for select all/none).
        $this->add_checkbox_controller(1);

        foreach ($this->_customdata as $resource) {
            if ($resource->deletioninprogress) {
                continue;
            }

            $attributes = ['group' => 1, 'class' => $resource->visible ? 'text-primary' : 'text-body-secondary'];
            $mform->addElement('advcheckbox', "resource[{$resource->id}]", $resource->name, null, $attributes);
        }

        $this->add_action_buttons(false, get_string('title', 'qbank_genai'));
    }

    /**
     * Extra validation: At least one file shall be selected.
     *
     * @param array $data Submitted data
     * @param array $files Not used here
     * @return array element name/error description pairs (if any)
     */
    public function validation($data, $files) {
        $errors = [];

        // Make sure at least one file is selected!
        $noneselected = true;

        foreach ($data["resource"] as $id => $selected) {
            if (boolval($selected)) {
                $noneselected = false;
            }
        }

        if ($noneselected) {
            $errors["resource"] = get_string('errormsg_noneselected', 'qbank_genai');
        }

        return $errors;
    }

}
