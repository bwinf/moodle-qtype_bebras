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
 * Bebras question renderer class.
 *
 * @package    qtype
 * @subpackage bebras
 * @copyright  2009 The Open University
 * @copyright  2021 BWINF
 * @author     Manuel Gundlach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for bebras questions.
 *
 * @copyright  2009 The Open University
 * @copyright  2021 BWINF
 * @author     Manuel Gundlach
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_bebras_renderer extends qtype_renderer {
    private function get_task_creator(string $grader, ?string $currentanswer,
                                      ?string $currentscratch, ?bool $can_save,
                                      ?array $args) {
        $in = [
            'answer' => $currentanswer,
            'scratch' => $currentscratch,
            'can_save' => $can_save
        ];
        $mode = 'create_form';
        $graderfile = 'graders/' . $grader . '.grader.php';
        if (!include $graderfile) {
            die('Error: Grader missing');
        }
        return $out;
    }
        
    private function iframe_encapsulate(string $srcdoc, string $id, bool $freeze) {
        $srcdoc = html_writer::tag('body', $srcdoc);
        
        $biberiframeattributes = array(
            'id' => 'biber-iframe-' . $id,
            'srcdoc' => $srcdoc,
            'frameborder' => '0',
            'width' => '100%'
        );
        if ($freeze) {
            $biberiframeattributes['style'] = 'pointer-events: none;';
        }
        $result = html_writer::tag('iframe', '', $biberiframeattributes);

        $iframeresizer  = 'function ifrresizer(event) {'
                        . 'let ifr = document.getElementById("biber-iframe-' . $id . '");'
                        . 'if(ifr.style.height != ifr.contentWindow.document.documentElement.scrollHeight+"px")'
                        . 'ifr.style.height = ifr.contentWindow.document.documentElement.scrollHeight+10+"px";'
                        . '}';
        $iframeresizerloader  = 'function(event) {'
                        . $iframeresizer
                        . 'let ifr = document.getElementById("biber-iframe-' . $id . '");'
                        . 'ifr.contentWindow.addEventListener("resize",ifrresizer);'
                        . 'ifrresizer();'
                        . 'setTimeout(ifrresizer, 1000);'
                        . '}';
        $scr  = 'window.addEventListener("load", ' . $iframeresizerloader . ');';
        $result .= html_writer::script($scr);
        
        return $result;
    }
        
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();
        $currentanswer = $qa->get_last_qt_var('answer');

        $inputname = $qa->get_qt_field_name('answer');

        // Remove scratch value from the answer and provide it separately
        $currentscratch = "";
        if(strpos($currentanswer, "!!!!!")){
            $currentscratch = substr($currentanswer,
                                     strpos($currentanswer, "!!!!!")+5);
            $currentanswer  = substr($currentanswer,
                                     0,
                                     strpos($currentanswer, "!!!!!"));
        }

        $inputattributes = array(
            'type' => 'text',
            'name' => $inputname,
            'value' => $currentanswer,
            'id' => $inputname,
            'class' => 'hidden',
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
        
        list($grader, $in0, $in1) = $question->get_grader($questiontext);
        $questiontext = substr($questiontext, 0, $in0) . substr($questiontext, $in1);
        
        list($args, $in0, $in1) = $question->get_args($questiontext);
        $questiontext = substr($questiontext, 0, $in0) . substr($questiontext, $in1);

        // Replace {TASK-CREATOR} in question text with the task creator
        // generated by the .grader.php file
        $questiontext = str_replace('{TASK-CREATOR}',
                                    $this->get_task_creator($grader,
                                                            $currentanswer,
                                                            $currentanswer,
                                                            !($options->correctness),
                                                            $args),
                                    $questiontext);
        
        // This isn't pretty, but we have to replace @@PLUGINFILE@@/... in
        // answers of 'Multiple Choice with Images' grader
        $questiontext = $question->format_text($questiontext,
                                               $question->questiontextformat,
                                               $qa,
                                               'question',
                                               'questiontext',
                                               $question->id);

        $inputinplace = $inputattributes['id'];
        $inputinplace = str_replace(':', '_', $inputinplace);

        // Scratch input
        $scratchinputattributes = array(
            'type' => 'text',
            'name' => "scratch_" . $inputinplace,
            'value' => $currentscratch,
            'id' => "scratch_" . $inputinplace,
            'class' => 'hidden',
            'readonly' => 'readonly',
        );

        $input  = html_writer::empty_tag('input', $inputattributes) . $feedbackimg; 
        $input .= html_writer::empty_tag('input', $scratchinputattributes);

        $srcdoc = $questiontext;
        
        if(!in_array($question->get_grader($questiontext)[0],
            array("Open Integer", "Open Int 2019", "Open Question"))){
            $biberanswerfieldattributes = array(
                'type' => 'hidden',
                'name' => 'answer',
                'value' => $currentanswer,
                'id' => 'answer',
                'readonly' => 'readonly',
            );
            $biberscratchfieldattributes = array(
                'type' => 'hidden',
                'name' => 'scratch',
                'value' => $currentscratch,
                'id' => 'scratch',
                'readonly' => 'readonly',
            );
            $srcdoc .= html_writer::empty_tag('input', $biberanswerfieldattributes);
            $srcdoc .= html_writer::empty_tag('input', $biberscratchfieldattributes);
        }
        
        $result = $this->iframe_encapsulate($srcdoc, $inputinplace, $options->correctness);
        $result = html_writer::tag('div', $result, array('class' => 'qtext'));

        $result .= html_writer::start_tag('div', array('class' => 'ablock form-inline hidden'));
        $result .= html_writer::tag('span', $input, array('class' => 'answer'),
                array('for' => $inputattributes['id']));
        $result .= html_writer::end_tag('div');

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error(array('answer' => $currentanswer)),
                    array('class' => 'validationerror'));
        }

        // NOTE This JS code needs getAnswer (and getScratch) to be defined
        // _globally_ in .grader.php. If it isn't already, e.g. because it's
        // part of a Task object, define it like e.g.
        // in 'Clickable SVG - Multi.grader.php'.
        $answercatcher  = 'function(event) {';
        $answercatcher .= '  var refo = document.getElementById("responseform");'
                        . '  if(refo){'
                        . '    refo.addEventListener("submit", function() {'
                        . '      console.log("Inserting answer... ");'
                        . '      let ifrcW = document.getElementById("biber-iframe-' . $inputinplace . '").contentWindow;'
                        . '      let answerfield = document.getElementById("' . $inputattributes['id'] . '");'
                        . '      answerfield.value = ifrcW.getAnswer();'
                        . '      if(ifrcW.getScratch){'
                        . '        var scratch = ifrcW.getScratch();'
                        . '        if(scratch){'
                        . '          answerfield.value += "!!!!!" + scratch;'
                        . '        }'
                        . '      }'
                        . '    });'
                        . '  }'
                        . '}';
        
        $scr = 'document.addEventListener("DOMContentLoaded", ' . $answercatcher . ');';
        $result .= html_writer::script($scr);

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
    
    protected function general_feedback(question_attempt $qa) {
        $feedback = $qa->get_question()->format_generalfeedback($qa);
        if(!$feedback)
            return '';
        
        $ifrid = $qa->get_qt_field_name('answer');
        $ifrid = str_replace(':', '_', $ifrid);
        $ifrid .= '-explanation';
        return $this->iframe_encapsulate($feedback, $ifrid, False);
    }
}
