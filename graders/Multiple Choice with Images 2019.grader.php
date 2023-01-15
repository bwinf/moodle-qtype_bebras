<?php

if($mode == 'grade'){
  if ($in['answer'] == ''){
    $out = 'noanswer';
  } else if (in_array($in['answer'], $args['correct_answers'])){
    $out = 'correct';
  } else{
    $out = 'incorrect';
  }
}

if($mode == 'check'){
    $formattedAnswers = [];
    foreach($args['answers'] as $answer){
        $answer = explode(',', $answer);
        $formattedAnswers[] = $answer[0];
    }
	$out = $in['answer'] == '' || in_array($in['answer'], $formattedAnswers);
}

if($mode == 'create_form'){
	$out = '';
    $out .= '
    <style>
        img.question {
            padding: 10px;
            min-height: 50px;
            min-width: 120px;
            width: auto;
            display: inline-block;
            margin: 0;
            margin-bottom: 10px;
            cursor: pointer;
            float: none !important;
        }
        
        img.question.selected {
            padding: 10px;
            min-height: 50px;
        }
        
        div.horizontal {
            display: table-row;
            width: 100%;
            text-align: left;
        }
        
        div.horizontal.answers {
            display: table;
            padding-top:8px;
            margin-bottom:5px;
            border: 0;
        }
        
        div.horizontal > img.question,
        div.horizontal > input[type=submit],
        div.horizontal > input.erase {
            float: left;
            margin-top: 0px !important;
            margin-left: 10px;
            margin-right: 10px;
        }
        
        div.horizontal > input.erase.full {
            margin-top: 0px !important;
        }
        
        div.vertical.answers > button.question.submit {
            display: block;
        }
    </style>';
    
    if(isset($args['grader']['shuffle'])){
        if($args['grader']['shuffle'] == 'true'){
            shuffle($args['answers']);
        }
    }
    
    $layoutmode = 'vertical';
    $olympic = false;
    $splitValue = 0;
    if(isset($args["grader"]) && isset($args["grader"]["layoutmode"])){
        if($args["grader"]["layoutmode"] === "olympic"){
            $layoutmode = "horizontal";
            $amount = count($args["answers"]);
            if($amount % 2 !== 0){
                
                $olympic = true;
                
                $amount = $amount / 2;
                $splitValue = round($amount, 0, PHP_ROUND_HALF_DOWN);
            }
        }else{
            $layoutmode = $args["grader"]["layoutmode"];
        }
    }
    
    $classes = "question submit";
    
//     $out .= '<input type="hidden" id="answer" name="answer" value="" />';
//     $out .= '<script type="text/javascript">function SaveAnswer(value) { var e = document.getElementById("answer"); e.value = value; e.form.submit(); }</script>';
    $out .= "<script type=\"text/javascript\">function SaveAnswer(value) { 
    document.getElementById(\"answer\").value = value; 
    document.getElementsByName(\"answerel\").forEach(function(el){ if(el.dataset.value==value){el.classList.add(\"image_selected\");}else{el.classList.remove(\"image_selected\");} }); }</script>\n";
    $out .= "<div class='$layoutmode answers'>";
    
    $counter = 0;
    foreach($args['answers'] as $answer){
//         $url = $helper->get_image_url($answer[0]);
        $url = '@@PLUGINFILE@@/' . $answer;
        
        $out .= '<img name="answerel" data-value="'.$answer.'" src="'.$url.'" class="'.$classes;
        $answer = explode(',', $answer);
        if($answer[0] === $in['answer']){
			$out .= ' selected';
        }
        $out .= '" style="';
        
        if(isset($args['grader']['style'])){
            $out .= $args['grader']['style'];
        }
        
        if(isset($answer[1])){
            $out .= $answer[1];
        }
        $out .= '" onclick="SaveAnswer(\''.$answer[0].'\');"/>';
        
        if($olympic){
            if($counter == $splitValue){
                $out .= '<div></div>';
            }
        }
        
        $counter++;
    }
    $out .= '<script>
    document.addEventListener("DOMContentLoaded", function(event) {
            getAnswer = function(){return document.getElementById("answer").value;};
    });
    </script>';
//     $out .= "</div>";
//     $out .= "<div class='$layoutmode'>";
//     $out .=	$helper->erase_button($in);
//     $out .= "</div>";
}

if($mode == 'handle_form'){
	if(isset($in['form']['unanswer'])){
		$out = '';
    }else if(isset($in['form']['answer'])){
		$out = $in['form']['answer'];
    }else{
		$out = false;
    }
}
