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
 * Interactive SVG question renderer class.
 *
 * @package    qtype
 * @subpackage interactivesvg
 * @copyright  2009 The Open University
 * @copyright  2021 BWINF
 * @author     Manuel Gundlach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for interactive SVG questions.
 *
 * @copyright  2009 The Open University
 * @copyright  2021 BWINF
 * @author     Manuel Gundlach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_interactivesvg_renderer extends qtype_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();
        $currentanswer = $qa->get_last_qt_var('answer');

        // Differentiate between generic and some special graders
        $conngraderstr = "<!--[Connector-Grader]-->";
        if(substr($question->format_questiontext($qa), 0,  strlen($conngraderstr)) === $conngraderstr){
            if(!$currentanswer || $currentanswer === ""){
                $currentanswer = '[]';
            }else{
                $currentanswer = urldecode($currentanswer);
                // "'\ are removed
                $currentanswer = str_replace(array('"', "'", "\\"), "", $currentanswer);
            }
        }
        else{
            $currentanswer = urldecode($currentanswer);
        }

        $inputname = $qa->get_qt_field_name('answer');

        // Remove scratch value from the answer and provide it separately
        $currentscratch = "";
        if(strpos($currentanswer, "!!!!!")){
            $currentscratch = substr($currentanswer, strpos($currentanswer, "!!!!!")+5);
            $currentanswer = substr($currentanswer, 0, strpos($currentanswer, "!!!!!"));
        }

        $inputattributes = array(
            'type' => 'text',
            'name' => $inputname,
            'value' => $currentanswer,
            'id' => $inputname,
            'class' => 'form-control d-inline',
            'readonly' => 'readonly',
        );

        $feedbackimg = '';
        if ($options->correctness) {
            $answer = $question->get_matching_answer(array('answer' => $currentanswer));
            if ($answer) {
                $fraction = $answer->fraction;
            } else {
                $fraction = 0;
            }
            $inputattributes['class'] .= ' ' . $this->feedback_class($fraction);
            $feedbackimg = $this->feedback_image($fraction);
        }

        $questiontext = $question->format_questiontext($qa);

        // Replace all occurences of {Q-ID} in question text with the input id,
        // where : is replaced with _
        $inputinplace = $inputattributes['id'];
        $inputinplace = str_replace(':', '_', $inputinplace);
        $questiontext = str_replace('{Q-ID}', $inputinplace, $questiontext);

        // Replace all occurences of {CURRENT-ANSWER} in question text with the current answer
        $questiontext = str_replace('{CURRENT-ANSWER}', json_encode($currentanswer), $questiontext);
        // Replace all occurences of {CURRENT-SCRATCH} in question text with the current scratch
        $questiontext = str_replace('{CURRENT-SCRATCH}', json_encode($currentscratch), $questiontext);

        // Scratch input
        $scratchinputattributes = array(
            'type' => 'text',
            'name' => "scratch_" . $inputinplace,
            'value' => $currentscratch,
            'id' => "scratch_" . $inputinplace,
            'class' => 'form-control d-inline',
            'readonly' => 'readonly',
        );

        $input = html_writer::empty_tag('input', $inputattributes) . $feedbackimg . html_writer::empty_tag('input', $scratchinputattributes);

        $result = html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        $result .= html_writer::start_tag('div', array('class' => 'ablock form-inline hidden'));
        $result .= html_writer::tag('label', get_string('answer', 'qtype_interactivesvg',
                html_writer::tag('span', $input, array('class' => 'answer'))),
                array('for' => $inputattributes['id']));
        $result .= html_writer::end_tag('div');

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error(array('answer' => $currentanswer)),
                    array('class' => 'validationerror'));
        }

        $scr = 'document.addEventListener("DOMContentLoaded", function(event) {';
        $scr .= 'document.querySelectorAll("[id=id_save_question_preview], [id=id_finish_question_preview], [id=mod_quiz-prev-nav], [id=mod_quiz-next-nav]").
                forEach(function(button_id){
                    button_id.addEventListener("click", function() {
                        let answerfield = document.getElementById("' . $inputattributes['id'] . '");
                        answerfield.value=escape(getAnswer_' . $inputinplace . '());
                        if(typeof getScratch_' . $inputinplace . ' === "function"){
                            answerfield.value += "!!!!!" + escape(getScratch_' . $inputinplace . '());
                        }
                    });
                });
            ';
        $scr .= '});';
        $result .= html_writer::tag('script', $scr);

        return $result;
    }

    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();

        $answer = $question->get_matching_answer(array('answer' => $qa->get_last_qt_var('answer')));
        if (!$answer || !$answer->feedback) {
            return '';
        }

        return $question->format_text($answer->feedback, $answer->feedbackformat,
                $qa, 'question', 'answerfeedback', $answer->id);
    }

    public function correct_response(question_attempt $qa) {
        return '';
    }
}
