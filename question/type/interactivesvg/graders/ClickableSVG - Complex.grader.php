<?php

if ($mode == 'grade') {
  if ($in['answer'] == '' || !isset($args['correct_answers'])) {
    $out = 'noanswer';
  } else {

    $out = 'incorrect';

    //Typecast so it does not turn into an object
    $answer = (array) json_decode($in['answer']);
    foreach($args['correct_answers'] as $line => $value) {
      $correctAnswer = [];
      if($value !== "-") {
//       We want to store json strings in Moodle's answers fields so they can be used for
//       the "Correct answer" feature. Therefore, we adapt how the entries are compared.
        $values = ((array) json_decode($value))["states"];
        $correctAnswer["states"] = $values;
      }

      //overrules the line in correct_answers
      if(isset($args["correct_answer_{$line}"])) {
        $def = $args["correct_answer_{$line}"];

        if(isset($def["states"])) $correctAnswer["states"] = $def["states"];
        if(isset($def["active"])) $correctAnswer["active"] = (int) $def["active"];
        if(isset($def["moves"])) $correctAnswer["moves"] = (int) $def["moves"];
        if(isset($def["combinations"])) $correctAnswer["combinations"] = count($def["combinations"]);
        if(isset($def["sum"])) $correctAnswer["sum"] = (int) $def["sum"];
      }

      if(isset($correctAnswer["states"])) {
        //turn "000" into ["0","0","0"]
        $states = [];
        foreach($correctAnswer["states"] as $state) {
          $states[] = (int) $state;
        }
        $correctAnswer["states"] = $states;
      }

      if($correctAnswer !== []) {
        $res = true;
        foreach($correctAnswer as $key => $nestedValue) {
          if($correctAnswer[$key] !== $answer[$key]) {
            $res = false;
          }
        }

        if($res) {
          $out = 'correct';
        }
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
    "countMoves" => false,
    "countValues" => false,
    "countActives" => false,
    "countCombinations" => false,
    "allowReset" => true,
    "defaultStates" => [],
    "width" => "800px",
    "height" => "800px",
    "values" => []
  ];

  if (isset($args['grader'])) {
    $g = $args['grader'];

    if(isset($g['src'])) $src = $g['src'];

    if(isset($g['countmoves'])) $p['countMoves'] = $g['countmoves'] === "true" ? true : false;
    if(isset($g['countvalues'])) $p['countValues'] = $g['countvalues'] === "true" ? true : false;
    if(isset($g['countactives'])) $p['countActives'] = $g['countactives'] === "true" ? true : false;
    if(isset($g['countcombinations'])) $p['countCombinations'] = $g['countcombinations'] === "true" ? true : false;
    if(isset($g['allowreset'])) $p['allowReset'] = $g['allowreset'] === "true" ? true : false;

    if(isset($g['width'])) $p['width'] = $g['width'];
    if(isset($g['height'])) $p['height'] = $g['height'];

    if(isset($g['defaultstates'])) {
      foreach(explode(",", str_replace(" ", "", $g['defaultstates'])) as $defaultState) {
        $p['defaultStates'][] = (int) $defaultState;
      }
    }
  }

  if(isset($args['values'])) {
    $v = $args['values'];

    foreach($v as $index => $val) {
      foreach(explode(",", $val) as $value) {
        $p['values'][$index][] = (int) $value;
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
      task = new ComplexClickableSVG("$src", JSON.parse("$p"));
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
