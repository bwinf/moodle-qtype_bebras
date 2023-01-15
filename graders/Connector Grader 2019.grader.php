<?php

if($mode == 'grade'){
    
    if($in['answer'] == '' || $in['answer'] == '%5B%5D'){
        $out = 'noanswer';
    }else{
        $answer = urldecode($in['answer']);
//         $answer = str_replace(array("\",\"", "\"", "[", "]"), array("-", "", "", ""), $answer);
        
        $out = 'incorrect';
        foreach($args['correct_answers'] as $value){
            $correctAnswer = str_replace(" ", "", $value);
            
            if($answer === $correctAnswer){
                $out = 'correct';
            }
            
        }
    }
    
    
}

if($mode == 'check'){
    $out = true;
}

if($mode == 'create_form'){
    
    $leftCol = '"50%"';
    $rightCol = '"50%"';
    $leftType = '"text"';
    $rightType = '"text"';
    $leftWidth = '"200px"';
    $rightWidth = '"200px"';
    
    if(isset($args["grader"])){
        
        if(isset($args["grader"]["leftcol"])){
            $leftCol = '"'.$args["grader"]["leftcol"].'%"';
        }
        
        if(isset($args["grader"]["rightcol"])){
            $rightCol = '"'.$args["grader"]["rightcol"].'%"';
        }
        
        if(isset($args["grader"]["lefttype"])){
            $leftType = '"'.$args["grader"]["lefttype"].'"';
        }
        
        if(isset($args["grader"]["righttype"])){
            $rightType = '"'.$args["grader"]["righttype"].'"';
        }
        
        if(isset($args["grader"]["leftwidth"])){
            $leftWidth = '"'.$args["grader"]["leftwidth"].'px"';
        }
        
        if(isset($args["grader"]["rightwidth"])){
            $rightWidth = '"'.$args["grader"]["rightwidth"].'px"';
        }
    }
    
    
    if(isset($args["lefttype"])){
        
        $i = 0;
        $leftCount = count($args["lefttype"]);
        
        $leftType = '[';
        
        foreach($args["lefttype"] as $value){
            $leftType .= '"'.$value.'"';
            
            if($i !== $leftCount - 1){
                $leftType .= ',';
            }
            
            $i++;
        }
        $leftType .= ']';
        
    }
    
    if(isset($args["righttype"])){
        
        $i = 0;
        $rightCount = count($args["righttype"]);
        
        $rightType = '[';
        
        foreach($args["righttype"] as $value){
            $rightType .= '"'.$value.'"';
            
            if($i !== $rightCount - 1){
                $rightType .= ',';
            }
            
            $i++;
        }
        $rightType .= ']';
    }
    
    $leftArray = '[]';
    $rightArray = '[]';
    
    if(isset($args["left"])){
        
        $i = 0;
        $leftCount = count($args["left"]);
        
        $leftArray = '[';
        
        foreach($args["left"] as $value){
            $leftArray .= '"'.$value.'"';
            
            if($i !== $leftCount - 1){
                $leftArray .= ',';
            }
            
            $i++;
        }
        $leftArray .= ']';
    }
    
    if(isset($args["right"])){
        
        $i = 0;
        $rightCount = count($args["right"]);
        
        $rightArray = '[';
        
        foreach($args["right"] as $value){
            $rightArray .= '"'.$value.'"';
            
            if($i !== $rightCount - 1){
                $rightArray .= ',';
            }
            
            $i++;
        }
        $rightArray .= ']';
    }
    
    
    
    
    $out = '
    <style>
        .connector_container {
            width: 100%;
            position: relative;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            align-items: center;
        }
        
        .connector_svg {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0px;
        }
        
        .connector_group {
            width: 50%;
        }
        
        .connector_item {
            margin: 10px 0;
            border: 1px solid black;
            border-radius: 5px;
            max-width: 80%;
            min-height: 25px;
            padding: 5px;
            position:relative;
            display: flex;
            align-items: center;
        }
        
        .connector_item.right {
            margin-right: 0;
            margin-left: auto;
            text-align: right;
        } 
    </style>
    ';
    
    $answer;
    
    if(!$in['answer'] || $in['answer'] === ""){
        $answer = '[]';
    }else{
        $answer = urldecode($in['answer']);
        $answer = str_replace(array('"', "'", "\\"), "", $answer);
    }
    
//     <script src="/shared/script/connector-library.js"></script>
    $out .= '
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            createTask('.$leftCol.', '.$rightCol.', '.$leftType.', '.$rightType.', '.$leftArray.', '.$rightArray.', '.$leftWidth.', '.$rightWidth.', '.$answer.');
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
