<?php

if($mode == 'grade'){
    if($in['answer'] == ''){
        $out = 'noanswer';
    }else{
//         $answer = str_replace(array("[", "]"), array("", ""), $in['answer']);
        $answer = $in['answer'];
        
        $out = 'incorrect';
        foreach($args['correct_answers'] as $correctAnswer){
            if($answer === $correctAnswer){
                $out = 'correct';
            }
            
            if($correctAnswer === '-' && $answer === ''){
                $out = 'correct';
            }
        }
    }
}

if($mode == 'check'){
    $out = true;
}

if($mode == 'create_form'){
    
    $mode = "false";
    if(isset($args['svg']['singlestate'])){
        $mode = $args['svg']['singlestate'];
    }
    
    $svgURL = "/question_files/1/4/c/interactive_svg_grader_default.svg";
    if(isset($args['svg']['url'])){
        $svgURL = /*'/question_files/'.*/$args['svg']['url'];
    }
    
    $svgWidth = "800px";
    if(isset($args['svg']['width'])){
        $svgWidth = $args['svg']['width'];
    }
    
    $svgHeight = "800px";
    if(isset($args['svg']['height'])){
        $svgHeight = $args['svg']['height'];
    }
    
    $svgActiveAmount = 0;
    if(isset($args['svg']['activeamount'])){
        $svgActiveAmount = $args['svg']['activeamount'];
    }
    
    $out = '';
    
//         <script src="/shared/script/clickable-svg-library.js"></script>
    $out .= '
        <script>
            document.addEventListener("DOMContentLoaded", function(event) {
                createTask("task-container", '.$mode.', "'.$svgURL.'", "'.$svgWidth.'", "'.$svgHeight.'", "'.$in['answer'].'", '.$svgActiveAmount.');
            });
        </script>
    ';
    
//     $out .= '<input type="hidden" name="answer" id="answer"  value="'. $in['answer'] . '" />';
//     $out .= '<input id="submit_answer" type="submit" class="submit full" value="'.__('save_button').'" onclick="document.getElementById(\'answer\').value=getAnswer();" />';
//     $out .= '<input type="submit" class="erase full" name="unanswer" value="'.__('erase_button').'" />';
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
