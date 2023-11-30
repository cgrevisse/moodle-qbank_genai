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

use moodleform;

require_once("$CFG->libdir/formslib.php");

/**
 * Class generation_form
 *
 * @package    qbank_genai
 * @copyright  2023 Christian Grévisse <christian.grevisse@uni.lu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generation_form extends moodleform {

    public function definition() {
        $mform = $this->_form;

        // add a checkbox controller for all checkboxes in `group => 1`: (necessary for select all/none)
        $this->add_checkbox_controller(1);

        foreach ($this->_customdata as $r) {
            $resource = (object) $r;
            $mform->addElement('advcheckbox', 'resource_'.$resource->id, $resource->name, null, ['group' => 1, 'class' => $resource->visible ? 'text-primary' : 'text-secondary'], [0, $resource->id]);
        }

        $this->add_action_buttons(false, get_string('title', 'qbank_genai'));
    }
    
    function validation($data, $files) {
        // Custom validation should be added here.
        return [];
    }

}
