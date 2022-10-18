<?php

if ($mode == 'grade') {
  if ($in['answer'] == '' || !isset($args['correct_answers'])) {
    $out = 'noanswer';
  } else {

    $out = 'incorrect';

    //Typecast so it does not turn into an object
    $answer = (array) json_decode($in['answer']);
    foreach($args['correct_answers'] as $value) {
//       We want to store json strings in Moodle's answers fields so they can be used for
//       the "Correct answer" feature. Therefore, we adapt how the entries are compared.
      $values = ((array) json_decode($value))["states"];

      $states = [];
      foreach($values as $state) {
        $states[] = (int) $state;
      }

      if($states === $answer["states"]) {
        $out = 'correct';
      }
    }
  }
}

if ($mode == 'check') {
  $out = true;
}

if ($mode == 'create_form') {
  $src = "";
  $p = [
//     "stylesheet" => "https://wettbewerb.informatik-biber.de//shared/style/clickablesvg-style.css",
    "stylesheet" => "data:text/css;base64," . base64_encode(
'svg {
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  font-family: Arial, Helvetica, sans-serif;
}

.csvg-reset {
  cursor: pointer;
}

.csvg-ignore {
  pointer-events: none;
}

.csvg-button {
  cursor: pointer;
}

.csvg-button.csvg-hidden {
  visibility: hidden;
}
'),
    "allowReset" => true,
    "defaultStates" => [],
    "width" => "800px",
    "height" => "800px"
  ];

  if (isset($args['grader'])) {
    $g = $args['grader'];

    if(isset($g['src'])) $src = $g['src'];

    if(isset($g['limit'])) $p['limit'] = (int) $g['limit'];
    if(isset($g['allowreset'])) $p['allowReset'] = $g['allowreset'] === "true" ? true : false;

    if(isset($g['width'])) $p['width'] = $g['width'];
    if(isset($g['height'])) $p['height'] = $g['height'];

    if(isset($g['defaultstates'])) {
      foreach(explode(",", str_replace(" ", "", $g['defaultstates'])) as $defaultState) {
        $p['defaultStates'][] = (int) $defaultState;
      }
    }
  }

  if($in['answer']) {
    $p['preset'] = json_decode($in['answer']);
  }

  $answer = htmlspecialchars($in['answer']);
  $p = addslashes(json_encode($p));

// <script src="/shared/script/clickablesvg-multi-lib.js"></script>
// <script src="https://unpkg.com/@webcomponents/webcomponentsjs@2.4.3/webcomponents-loader.js"></script>
  $out = <<<NOWDOC
<script>
  document.addEventListener("DOMContentLoaded", function(event) {
      task = new MultiClickableSVG("$src", JSON.parse("$p"));
      getAnswer = function(){return JSON.stringify(task.getAnswer());};
  });
</script>

NOWDOC;

}

if ($mode == 'handle_form') {
  if (isset($in['form']['unanswer'])) {
    $out = '';
  } else if (isset($in['form']['answer'])) {
    $out = $in['form']['answer'];
  } else {
    $out = false;
  }
}
