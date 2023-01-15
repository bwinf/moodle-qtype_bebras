<?php

if($mode == 'grade'){
    if($in['answer'] == ''){
        $out = 'noanswer';
    }else{
        $out = 'incorrect';
        foreach($args['correct_answers'] as $correctAnswer){
            if($in['answer'] === str_replace(" ", "", $correctAnswer)){
                $out = 'correct';
            }
        }
    }
}

if($mode == 'check'){
    $out = true;
}

if($mode == 'create_form'){
    
    $svgURL = "/question_files/a/b/5/draggable_svg_grader_default.svg";
    if(isset($args['svg']['url'])){
        $svgURL = /*'/question_files/'.*/$args['svg']['url'];
    }
    
    $svgWidth = "740px";
    if(isset($args['svg']['width'])){
        $svgWidth = $args['svg']['width'];
    }
    
    $svgHeight = "";
    if(isset($args['svg']['height'])){
        $svgHeight = $args['svg']['height'];
    }
    
    $svgDropmode = "snap";
    if(isset($args['svg']['dropmode'])){
        $svgDropmode = $args['svg']['dropmode'];
    }
    
    $svgStartContainers = "";
    if(isset($args['svg']['start'])){
        $svgStartContainers = $args['svg']['start'];
    }
    
    
    $out = '';
    
//     <script src="/shared/script/draggable-svg-library.js"></script>
    $out = '
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            createTask("task-container", "'.$svgURL.'", "'.$svgWidth.'", "'.$svgHeight.'", "'.$svgDropmode.'", ['.$svgStartContainers.'], "'.$in['answer'].'", "'.$in['scratch'].'");
        });
    </script>
    ';
    
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
