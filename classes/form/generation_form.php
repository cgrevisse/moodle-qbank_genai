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

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot. '/question/bank/genai/classes/handler/handler.php');

use moodleform;
use qbank_genai\handler\HandlerRegistry;

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

        $supported_types = HandlerRegistry::get_registry()->get_supported_types();

        // add a checkbox controller for all checkboxes in `group => 1`: (necessary for select all/none)
        $this->add_checkbox_controller(1);

        foreach ($this->_customdata as $resource) {
            $fileinfo = get_fileinfo_for_resource($resource->id);

            if (in_array($fileinfo->extension, $supported_types)) {
                $mform->addElement('advcheckbox', "resource[{$resource->id}]", $resource->name, null, ['group' => 1, 'class' => $resource->visible ? 'text-primary' : 'text-body-secondary']);
            }
        }
        
        $this->add_action_buttons(false, get_string('title', 'qbank_genai'));
    }
    
    function validation($data, $files) {
        $errors = [];
        
        // make sure at least one file is selected!
        $noneselected = true;

        foreach($data["resource"] as $id => $selected) {
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
