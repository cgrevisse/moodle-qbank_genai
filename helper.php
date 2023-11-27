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
 * TODO describe file helper
 *
 * @package    qbank_genai
 * @copyright  2023 Christian Grévisse <christian.grevisse@uni.lu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function get_multichoice_question_data() {
    global $USER;

    $qdata = new stdClass();

    $qdata->createdby = $USER->id;
    $qdata->modifiedby = $USER->id;
    $qdata->qtype = 'multichoice';
    $qdata->name = 'Multiple choice question';
    $qdata->questiontext = 'Which is the oddest number?';
    $qdata->questiontextformat = FORMAT_HTML;
    $qdata->generalfeedback = 'The oddest number is One.';
    $qdata->generalfeedbackformat = FORMAT_HTML;
    $qdata->defaultmark = 1;
    $qdata->length = 1;
    $qdata->penalty = 0.3333333;
    $qdata->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
    $qdata->versionid = 0;
    $qdata->version = 1;
    $qdata->questionbankentryid = 0;

    $qdata->options = new stdClass();
    $qdata->options->shuffleanswers = 1;
    $qdata->options->answernumbering = '123';
    $qdata->options->showstandardinstruction = 0;
    $qdata->options->layout = 0;
    $qdata->options->single = 1;
    $qdata->options->correctfeedback = '';
    $qdata->options->correctfeedbackformat = FORMAT_HTML;
    $qdata->options->partiallycorrectfeedback = '';
    $qdata->options->partiallycorrectfeedbackformat = FORMAT_HTML;
    $qdata->options->shownumcorrect = 1;
    $qdata->options->incorrectfeedback = '';
    $qdata->options->incorrectfeedbackformat = FORMAT_HTML;

    $qdata->options->answers = array(
        13 => (object) array(
            'id' => 13,
            'answer' => 'One',
            'answerformat' => FORMAT_PLAIN,
            'fraction' => 1,
            'feedback' => 'One is the oddest.',
            'feedbackformat' => FORMAT_HTML,
        ),
        14 => (object) array(
            'id' => 14,
            'answer' => 'Two',
            'answerformat' => FORMAT_PLAIN,
            'fraction' => 0.0,
            'feedback' => 'Two is even.',
            'feedbackformat' => FORMAT_HTML,
        ),
        15 => (object) array(
            'id' => 15,
            'answer' => 'Three',
            'answerformat' => FORMAT_PLAIN,
            'fraction' => 0,
            'feedback' => 'Three is odd.',
            'feedbackformat' => FORMAT_HTML,
        ),
        16 => (object) array(
            'id' => 16,
            'answer' => 'Four',
            'answerformat' => FORMAT_PLAIN,
            'fraction' => 0.0,
            'feedback' => 'Four is even.',
            'feedbackformat' => FORMAT_HTML,
        ),
    );

    $qdata->hints = array(
        1 => (object) array(
            'hint' => 'Hint 1.',
            'hintformat' => FORMAT_HTML,
            'shownumcorrect' => 1,
            'clearwrong' => 0,
            'options' => 0,
        ),
        2 => (object) array(
            'hint' => 'Hint 2.',
            'hintformat' => FORMAT_HTML,
            'shownumcorrect' => 1,
            'clearwrong' => 1,
            'options' => 1,
        ),
    );

    return $qdata;
}

function get_multichoice_question_form_data() {
    $qdata = new stdClass();

    $qdata->name = 'multiple choice question';
    $qdata->questiontext = array('text' => 'Which is the oddest number?', 'format' => FORMAT_HTML);
    $qdata->generalfeedback = array('text' => 'The oddest number is One.', 'format' => FORMAT_HTML);
    $qdata->defaultmark = 1;
    $qdata->noanswers = 5;
    $qdata->numhints = 2;
    $qdata->penalty = 0.3333333;
    $qdata->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
    $qdata->versionid = 0;
    $qdata->version = 1;
    $qdata->questionbankentryid = 0;

    $qdata->shuffleanswers = 1;
    $qdata->answernumbering = '123';
    $qdata->showstandardinstruction = 0;
    $qdata->single = '1';
    $qdata->correctfeedback = array('text' => '',
                                    'format' => FORMAT_HTML);
    $qdata->partiallycorrectfeedback = array('text' => '',
                                             'format' => FORMAT_HTML);
    $qdata->shownumcorrect = 1;
    $qdata->incorrectfeedback = array('text' => '',
                                      'format' => FORMAT_HTML);
    $qdata->fraction = array('1.0', '0.0', '0.0', '0.0', '0.0');
    $qdata->answer = array(
        0 => array(
            'text' => 'One',
            'format' => FORMAT_PLAIN
        ),
        1 => array(
            'text' => 'Two',
            'format' => FORMAT_PLAIN
        ),
        2 => array(
            'text' => 'Three',
            'format' => FORMAT_PLAIN
        ),
        3 => array(
            'text' => 'Four',
            'format' => FORMAT_PLAIN
        ),
        4 => array(
            'text' => '',
            'format' => FORMAT_PLAIN
        )
    );

    $qdata->feedback = array(
        0 => array(
            'text' => 'One is the oddest.',
            'format' => FORMAT_HTML
        ),
        1 => array(
            'text' => 'Two is even.',
            'format' => FORMAT_HTML
        ),
        2 => array(
            'text' => 'Three is odd.',
            'format' => FORMAT_HTML
        ),
        3 => array(
            'text' => 'Four is even.',
            'format' => FORMAT_HTML
        ),
        4 => array(
            'text' => '',
            'format' => FORMAT_HTML
        )
    );

    $qdata->hint = array(
        0 => array(
            'text' => 'Hint 1.',
            'format' => FORMAT_HTML
        ),
        1 => array(
            'text' => 'Hint 2.',
            'format' => FORMAT_HTML
        )
    );
    $qdata->hintclearwrong = array(0, 1);
    $qdata->hintshownumcorrect = array(1, 1);

    return $qdata;
}

function create_question_category($courseid) {
    global $DB;

    $record = [
        'name'       => 'GenAI Test Category',
        'info'       => '',
        'infoformat' => FORMAT_HTML,
        'stamp'      => make_unique_id_code(),
        'sortorder'  => 999,
        'idnumber'   => null,
        'contextid'  => $courseid,
        'parent'     => question_get_top_category($courseid, true)->id
    ];

    $record['id'] = $DB->insert_record('question_categories', $record);
    return (object) $record;
}

function get_question_editing_form($cat, $questiondata) {
    $catcontext = context::instance_by_id($cat->contextid, MUST_EXIST);
    $contexts = new core_question\local\bank\question_edit_contexts($catcontext);
    $dataforformconstructor = new stdClass();
    $dataforformconstructor->createdby = $questiondata->createdby;
    $dataforformconstructor->qtype = $questiondata->qtype;
    $dataforformconstructor->contextid = $questiondata->contextid = $catcontext->id;
    $dataforformconstructor->category = $questiondata->category = $cat->id;
    $dataforformconstructor->status = $questiondata->status;
    $dataforformconstructor->formoptions = new stdClass();
    $dataforformconstructor->formoptions->canmove = true;
    $dataforformconstructor->formoptions->cansaveasnew = true;
    $dataforformconstructor->formoptions->canedit = true;
    $dataforformconstructor->formoptions->repeatelements = true;
    $qtype = question_bank::get_qtype($questiondata->qtype);
    return  $qtype->create_editing_form('question.php', $dataforformconstructor, $cat, $contexts, true);
}
