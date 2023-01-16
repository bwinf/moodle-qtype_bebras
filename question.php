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
 * Bebras question definition class.
 *
 * @package    qtype_bebras
 * @subpackage bebras
 * @copyright  2009 The Open University
 * @copyright  2021 BWINF
 * @author     Manuel Gundlach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/questionbase.php');
require_once($CFG->dirroot . '/question/type/shortanswer/question.php');

/**
 * Represents a bebras question.
 *
 * @copyright  2009 The Open University
 * @copyright  2021 BWINF
 * @author     Manuel Gundlach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_bebras_question extends qtype_shortanswer_question implements question_response_answer_comparer {
    private function is_correct(
        string $grader, ?string $currentanswer,
        ?string $correctanswer
    ) {
        $in = ['answer' => $currentanswer];
        // Hack: We set the list correct_answers to contain only the element
        // $correctanswer, so the grader will only consider this one and we know
        // whether it's a correct answer.
        $args['correct_answers'] = [$correctanswer];
        $mode = 'grade';
        $graderfile = 'graders/' . $grader . '.grader.php';
        if (!include($graderfile)) {
            die('Error: Grader missing');
        }
        return $out == 'correct';
    }

    /**
     * Gives the name of the Bebras grader
     *
     * The questiontext is parsed to find the .grader.php filename (without the extension).
     * It is returned along with its position in the questiontext.
     *
     * @param string $questiontext The raw questiontext that contains the grader info section
     * @return array The name of the grader, and the start and end index of the grader section in the questiontext
     */
    public function get_grader(string $questiontext) {
        // Parse grader.
        $in0 = strpos($questiontext, "{GRADER-START}");
        $in1 = strpos($questiontext, "{GRADER-END}");
        $grader = substr($questiontext, $in0 + 14, $in1 - $in0 - 14);

        return array($grader, $in0, $in1 + 12);
    }

    /**
     * Gives the Bebras question's arguments
     *
     * The questiontext is parsed to find the JSON dump containing the question's arguments.
     * It is returned along with its position in the questiontext.
     *
     * @param string $questiontext The raw questiontext that contains the grader args section
     * @return array The arguments for the question, and the start and end index of the args section in the questiontext
     */
    public function get_args(string $questiontext) {
        // Parse arguments.
        $in0 = strpos($questiontext, "{ARGS-START}");
        $in1 = strpos($questiontext, "{ARGS-END}");
        $argstext = substr($questiontext, $in0 + 12, $in1 - $in0 - 12);

        $args = json_decode($argstext, true);

        return array($args, $in0, $in1 + 10);
    }

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_bebras');
    }

    public function compare_response_with_answer(array $response, question_answer $answer) {
        if (!array_key_exists('answer', $response) || is_null($response['answer'])) {
            return false;
        }

        $responseanswer = $response['answer'];

        // Remove scratch value from the answer.
        if (strpos($responseanswer, "!!!!!")) {
            $responseanswer = substr(
                $responseanswer,
                0,
                strpos($responseanswer, "!!!!!")
            );
        }

        return $this->is_correct($this->get_grader($this->questiontext)[0], $responseanswer, $answer->answer);
    }

    // We don't want e.g. the mathjax or link filter, so we disable all
    // formatting filters by overriding the format_text function with the
    // small change below.
    public function format_text(
        $text,
        $format,
        $qa,
        $component,
        $filearea,
        $itemid,
        $clean = false
    ) {
        $formatoptions = new stdClass();
        $formatoptions->noclean = !$clean;
        $formatoptions->para = false;
        // Disable all filters.
        $formatoptions->filter = false;
        $text = $qa->rewrite_pluginfile_urls($text, $component, $filearea, $itemid);
        return format_text($text, $format, $formatoptions);
    }

    public function get_correct_response() {
        // Giving a correct answer doesn't currently work with these graders.
        // Not sure about the Draggable grader in general. Also, not sure
        // about ClickableSVG - Complex in the complicated form.
        // "Lodge-Grader (1)" doesn't always work.
        if (
            in_array(
                $this->get_grader($this->questiontext)[0],
                ["Lodge-Scratch-Grader", "BBS Task 2019"]
            )
        ) {
            return null;
        }

        return parent::get_correct_response();
    }
}
