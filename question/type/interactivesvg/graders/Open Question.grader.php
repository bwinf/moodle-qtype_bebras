<?php

if ($mode == 'grade') {
	$filters = array("trim", "strtolower");
	foreach ($filters as $filter)
		$in["answer"] = $filter($in["answer"]);

	if ($in['answer'] == '')
		$out = 'noanswer';
	else if (in_array($in['answer'], $args['correct_answers']))
		$out = 'correct';
	else
		$out = 'incorrect';
}

if ($mode == 'check') {
	$out = true;
}

if ($mode == 'create_form') {
	$out = '<input type="text" name="answer" id="answer" class="full text open" style="width: 50px;" value="'. $in['answer'] . '" /><br />';
	$out .= '<script>
	document.addEventListener("DOMContentLoaded", function(event) {
	getAnswer = function(){return document.getElementById("answer").value;};
	});
	</script>';
// 	$out .=	$helper->default_buttons($in);
}

if ($mode == 'handle_form') {
	if (isset($in['form']['unanswer']))
		$out = '';
	else if (isset($in['form']['answer']))
		$out = $in['form']['answer'];
	else
		$out = false;
}
