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

namespace qbank_genai\event;

use core\session\exception;

/**
 * Event generation_launched
 *
 * @package    qbank_genai
 * @copyright  2023 Christian Grévisse <christian.grevisse@uni.lu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generation_launched extends \core\event\base {

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
    }

    /**
     * Event name.
     *
     * @return string
     */
    public static function get_name() {
        return "Question generation launched";
    }

    /**
     * Event description.
     *
     * @return string
     */
    public function get_description() {
        if (!array_key_exists('ids', $this->data['other'])) {
            return "";
        }

        $ids = implode(", ", $this->data['other']['ids']);
        return "The user with id '{$this->data['userid']}' launched the generation task for resources with ids {$ids}.";
    }
}
