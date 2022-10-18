<?php

if($mode == 'grade'){
    if($in['answer'] == ''){
        $out = 'noanswer';
    }else{
        // Check for answers in the form [[config...],[result...]]
        $pos = strrpos($in['answer'],"%5D%2C%5B");
        if($pos === false){	     
            if(in_array($in['answer'], $args['correct_answers'])){
                $out = 'correct';
            }else{
                $out = 'incorrect';
            }
        }else{ // Compare only the end of the answer, not the config
            $out = 'incorrect';
            foreach($args['correct_answers'] as $correct_answer){
                if(substr($in['answer'],$pos) == substr($correct_answer, strrpos($correct_answer,"%5D%2C%5B"))){
                    $out = 'correct';
                }
            }
        }
    }
}

if($mode == 'check'){
    $out = true;
}

if($mode == 'create_form'){
    $out = '<script>
  document.addEventListener("DOMContentLoaded", function(event) {
      getAnswer = function(){return escape(task.getAnswer());};
      getScratch = function(){
        if(task.getScratch){
          return escape(task.getScratch());
        }else{
          return null;
        }
      };
  });
</script>';
//     $out = '<script src="/question_files/5/4/3/lodge2014.js"></script>';
// TODO Why isn't this needed?
//     $out .= '<script src="/question_files/e/3/d/dragdroptouch.js"></script>';
    
//     $out .= '<br><input id="submit_answer" type="submit" class="submit full" value="'.__('@save_button').'" onclick="document.getElementById(\'answer\').value=escape(task.getAnswer()); if(task.getScratch){ document.getElementById(\'scratch\').value=escape(task.getScratch()); }" /><br />';
//     $out .= '<input type="submit" class="erase full" name="unanswer" value="'.__('@erase_button').'" onclick="document.getElementById(\'answer\').value = \'\'; document.getElementById(\'scratch\').value = \'\';">';
}

if($mode == 'handle_form'){
    if (isset($in['form']['unanswer'])){
        $out = '';
    }else if (isset($in['form']['answer'])){
        $out = $in['form']['answer'];
    }else{
        $out = false;
    }
}
