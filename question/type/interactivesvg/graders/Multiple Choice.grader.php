<?php

if ($mode == 'grade') {
	if ($in['answer'] == '')
		$out = 'noanswer';
	else if (in_array($in['answer'], $args['correct_answers']))
		$out = 'correct';
	else
		$out = 'incorrect';
}

if ($mode == 'check') {
	$out = $in['answer'] == '' || in_array($in['answer'], $args['answers']);
}

if ($mode == 'create_form') {
	$out = '';
	if (isset($args['grader']) && isset($args['grader']['shuffle']))
		shuffle($args['answers']);

	$nobr = isset($args["grader"]) && isset($args["grader"]["nobr"]);

	$out .= "<script type=\"text/javascript\">function SaveAnswer(value) {
	document.getElementById(\"answer\").value = value;
	document.getElementsByName(\"answer\").forEach(function(el){ if(el.value==value){el.classList.add(\"selected\");}else{el.classList.remove(\"selected\");} }); }</script>\n";
	
	foreach ($args['answers'] as $answer) {
		$out .= '<input type="submit" name="answer"';
		$out .= sprintf(" onclick=\"SaveAnswer(&quot;%s&quot;);\"", htmlspecialchars($answer));
		if (!$in['can_save'])
			$out .= ' disabled="true"';
		$out .= ' class="submit full question';
		if ($answer == $in['answer'])
			$out .= ' selected';
		$out .= '" value="' . htmlspecialchars($answer) . '" />';
		if (!$nobr)
			$out .= "<br />\n";
	}
	if ($nobr)
		$out .= "<br />\n";
	$out .= '<script>
	document.addEventListener("DOMContentLoaded", function(event) {
		getAnswer = function(){return document.getElementById("answer").value;};
	});
	</script>';
// <!-- 	$out .=	$helper->erase_button($in); -->
}

if ($mode == 'handle_form') {
	if (isset($in['form']['unanswer']))
		$out = '';
	else if (isset($in['form']['answer']))
		$out = $in['form']['answer'];
	else
		$out = false;
}
