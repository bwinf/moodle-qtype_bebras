<?php

switch ($mode)
{
	case "grade":
		if ($in["answer"] == "")
			$out = "noanswer";
		else if (in_array($in["answer"], $args["correct_answers"]))
			$out = "correct";
		else
			$out = "incorrect";
		break;

	case "check":
		$out = $in["answer"] == "" || in_array($in["answer"], $args["answers"]);
		break;

	case "create_form":
		if (isset($args["grader"]) && isset($args["grader"]["shuffle"]))
			shuffle($args["answers"]);

		$style = "";
		if (isset($args["grader"]) && isset($args["grader"]["style"]))
			$style = " style=\"" . $args["grader"]["style"] . "\"";

		$nobr = isset($args["grader"]) && isset($args["grader"]["nobr"]);

		$extra = "";
		if (!$in["can_save"])
			$extra = " disabled=\"true\"";

		$out = "";

// 		$out .= "<input type=\"hidden\" id=\"answer\" name=\"answer\" value=\"\" />\n";
// 		$out .= "<script type=\"text/javascript\">function SaveAnswer(value) { var e = document.getElementById(\"answer\"); e.value = value; e.form.submit(); }</script>\n";
		$out .= "<script type=\"text/javascript\">function SaveAnswer(value) { " .
		"document.getElementById(\"answer\").value = value; " .
		"document.getElementsByName(\"answerel\").forEach(function(el){ if(el.dataset.value==value){el.classList.add(\"image_selected\");}else{el.classList.remove(\"image_selected\");} }); }</script>\n";

		foreach ($args["answers"] as $answer)
		{
			$class = "submit image question";
			if ($answer == $in["answer"])
				$class .= " image_selected";

// 			$url = $helper->get_image_url($answer);
			$url = '@@PLUGINFILE@@/' . $answer;
			$out .= sprintf("<a href=\"javascript:SaveAnswer('%s')\"><img name=\"answerel\" data-value=\"%s\" src=\"%s\" class=\"%s\"%s%s /></a>\n",
				$answer, $answer, $url, $class, $style, $extra);

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
// 		$out .=	$helper->erase_button($in);
		break;

	case "handle_form":
		if (isset($in["form"]["unanswer"]))
			$out = "";
		else if (isset($in["form"]["answer"]))
			$out = $in["form"]["answer"];
		else
			$out = false;
		break;
}
