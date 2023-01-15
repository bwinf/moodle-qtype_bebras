<?php

if($mode === 'grade'){
    if($in['answer'] == ''){
        $out = 'noanswer';
    }else{
        $out = 'incorrect';
        foreach($args['correct_answers'] as $correctAnswer){
            if($correctAnswer === $in['answer']){
                $out = 'correct';
            }
        }
    }
}

if($mode == 'check'){
    $out = true;
}

if($mode === 'create_form'){
	$out = '';
//     $out = '<script src="/shared/script/bbs-task-library.js"></script>';
//     $out .= '<input type="hidden" name="answer" id="answer"  value="'. $in['answer'] . '" />';
//     $out .= '<input type="hidden" name="scratch" id="scratch"  value="'. $in['scratch'] . '" />';
    
//     $out .= '<br><input id="submit_answer" type="submit" class="submit full" value="'.__('save_button').'" onclick="document.getElementById(\'answer\').value=escape(getAnswer()); if(getScratch != undefined){ document.getElementById(\'scratch\').value=escape(getScratch()); }" />';
//     $out .= '<input type="submit" class="erase full" name="unanswer" value="'.__('erase_button').'" onclick="document.getElementById(\'answer\').value = \'\'; document.getElementById(\'scratch\').value = \'\';">';
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
