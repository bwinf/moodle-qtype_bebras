<?php

if($mode == 'grade'){
	if ($in['answer'] == ''){
		$out = 'noanswer';
    }else if (in_array($in['answer'], $args['correct_answers'])){
		$out = 'correct';
    }else{
		$out = 'incorrect';
    }
}

if($mode == 'check'){
	$out = $in['answer'] === '' || in_array($in['answer'], $args['answers']);
}

if($mode == 'create_form'){
	$out = '';
    $out .= '
    <style>
        button.question {
            min-height: 70px;
            min-width: 120px;
            width: auto;
            display: inline-block;
            padding: 10px 15px;
            margin: 0;
            margin-bottom: 10px;
            font-weight: bold;
            cursor: pointer;
            font-size: 15px;
            /*float: none !important;*/
        }
        
        button.question.selected {
            min-height: 50px;
			font-weight: bold;
			color: #FFFFFF !important;
			border-color: #ff643a;
            background-color: #ff643a;
            background: #ff643a !important;
        }
        
        div.horizontal {
            /*display: table-row;*/
            width: 100%;
            text-align: center;
        }
        
        div.horizontal.answers {
            /*display: table-row;*/
            padding-top:8px;
            margin-bottom:5px;
            border: 0;
        }
        
        div.horizontal > button.question,
        div.horizontal > input[type=submit],
        div.horizontal > input.erase  {
            /*float: left;*/
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
    
    if(isset($args["grader"]) && isset($args["grader"]["shuffle"])){
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
    
    
    if($layoutmode !== "dropdown"){
        $classes = "question submit";                
        $out .= "<div class='$layoutmode answers'>";
        
        $counter = 0;
        foreach($args['answers'] as $answer){
            $out .= '<button';
            if(!$in['can_save']){
                $out .= ' disabled="true"';
            }
            $out .= ' name="answerel"';
            $out .= ' class="'.$classes;
            if($answer === $in['answer']){
                $out .= ' selected';
            }
        
        	if(isset($args['grader']['style'])){
				$out .= '" style="';
            	$out .= $args['grader']['style'];
        	}
			
            $out .= '" onclick="SaveAnswer(\'';
            
            $foundNeedle = false;
            $charList = array('\\', '\'', '\"');
            foreach($charList as $needle){
                if(strpos($answer, $needle) !== false){
                    $foundNeedle = true;
                }
            }
            
            if($foundNeedle){
                $out .= addslashes($answer);
            }else{
                $out .= htmlspecialchars($answer);
            }
            
            $out .= '\', \'' . $answer;
            
            
            $out .= '\');">'.$answer.'</button>';
            
            if($olympic){
                if($counter == $splitValue){
                    $out .= '<div></div>';
                }
            }
            
            
            $counter++;
        }
        $out .= "</div>";
        
//         $out .= '<input type="hidden" id="answer" name="answer" value="" />';
        
        
        $out .= "<script type=\"text/javascript\">
        function SaveAnswer(value, answer) {
            document.getElementById(\"answer\").value = value;
            document.getElementsByName(\"answerel\").forEach(function(el){ if(el.textContent==answer){el.classList.add(\"selected\");}else{el.classList.remove(\"selected\");} });
        }
        </script>";
    
        $out .= '<script>
        document.addEventListener("DOMContentLoaded", function(event) {
                getAnswer = function(){return document.getElementById("answer").value;};
        });
        </script>';
    }else{
        // TODO Not supported yet!
        
        $out .= '<select style="width: auto; float: none;" name="answer">';
        
        foreach($args['answers'] as $answer){
            
            $out .= '<option value="';
            
            $foundNeedle = false;
            $charList = array('\\', '\'', '\"');
            foreach($charList as $needle){
                if(strpos($answer, $needle) !== false){
                    $foundNeedle = true;
                }
            }
            
            if($foundNeedle){
                $out .= addslashes($answer);
            }else{
                $out .= htmlspecialchars($answer);
            }
            
            $out .= '"';
            
            if($answer === $in['answer']){
                $out .= ' selected';
            }
            
            $out .= '>'.$answer.'</option>';
            
        }
        $out .= '</select>';
    }
    
//     $out .= "<div>";
//     if($layoutmode == "dropdown"){
//         $out .= $helper->save_button($in);
//     }
//     
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
