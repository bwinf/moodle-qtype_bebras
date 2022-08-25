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
 * Interactive SVG question definition class.
 *
 * @package    qtype
 * @subpackage interactivesvg
 * @copyright  2009 The Open University
 * @copyright  2021 BWINF
 * @author     Manuel Gundlach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/questionbase.php');
require_once($CFG->dirroot . '/question/type/shortanswer/question.php');

/**
 * Represents an interactive SVG question.
 *
 * @copyright  2009 The Open University
 * @copyright  2021 BWINF
 * @author     Manuel Gundlach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_interactivesvg_question extends qtype_shortanswer_question
        implements question_response_answer_comparer {

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_interactivesvg');
    }

    public function compare_response_with_answer(array $response, question_answer $answer) {
        if (!array_key_exists('answer', $response) || is_null($response['answer'])) {
            return false;
        }
        // Decode the answer
        $response_answer = $response['answer'];
        $response_answer = urldecode($response_answer);

        // Remove scratch value from the answer
        if(strpos($response_answer, "!!!!!")){
            $response_answer = substr($response_answer, 0, strpos($response_answer, "!!!!!"));
        }

        return self::compare_string_with_wildcard(
                $response_answer, $answer->answer, !$this->usecase);
    }
}
