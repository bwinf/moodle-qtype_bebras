<?php

if ($mode == 'grade') {
	$filters = array("trim", "strtolower");
	foreach ($filters as $filter)
		$in["answer"] = $filter($in["answer"]);

	if ($in['answer'] == '')
		$out = 'noanswer';
        $minvalue=1.0;
        $maxvalue=0.0;
        $float_answer = floatval (str_replace(",",".",$in['answer']));

	if (isset($args['grader']) && isset($args['grader']['minvalue']))
		$minvalue=floatval($args['grader']['minvalue']);
	if (isset($args['grader']) && isset($args['grader']['maxvalue']))
		$maxvalue=floatval($args['grader']['maxvalue']);

        if (in_array($in['answer'], $args['correct_answers'], true))
		$out = 'correct';
        else if (($float_answer>=$minvalue) && ($float_answer<=$maxvalue))
                $out = 'correct';
	else if ($in['answer'] != '')
		$out = 'incorrect';
}

if ($mode == 'check') {
	$out = true;
}

if ($mode == 'create_form') {
        $unit="";
	if (isset($args['grader']) && isset($args['grader']['unit']))
		$unit=$args['grader']['unit'];
 $out = "<p>Gib die richtige Zahl hier ein.</p>";
    $out.='<input type="number" style="width: 70px;" maxlength="10" name="answer" pattern="[0-9]*" id="answer" class="full text open" title="Bitte gib eine Zahl ein." value="'. $in['answer'] . '" /> '.$unit.'<br />';
    $out .= '<script>
  document.addEventListener("DOMContentLoaded", function(event) {
      getAnswer = function(){return document.getElementById("answer").value;};
  });
</script>';
 /*   $out.='<script>answer.oninvalid = function(event) {  event.target.setCustomValidity("'.__('please_enter_a_number').'");}</script>';
*/
// $out .=	$helper->default_buttons($in);
}

if ($mode == 'handle_form') {
	if (isset($in['form']['unanswer']))
		$out = '';
	else if (isset($in['form']['answer']))
		$out = $in['form']['answer'];
	else 
		$out = false;
}
