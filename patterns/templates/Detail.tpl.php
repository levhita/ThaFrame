<?php
  $Vars = $Data->PatternVariables;
  
  if($Vars->form_title){
    echo "<h3>".t($Vars->form_title)."</h3>";
  }
  if ($Vars->before_text) {
    echo "<p>".t($Vars->before_text)."</p>\n";
  }
  echo "<ul>";  
  
  foreach($Data->fields as $field=>$properties){
    $Properties = (object) $properties;
    if ($properties->type != 'separator') {
      echo "<li><strong>".t($Properties->label).":</strong> ";
      switch ($Properties->type) {
        case 'text':
          echo htmlspecialchars($Properties->value);
          break;
        case 'textarea':
          echo "<pre>".htmlspecialchars($Properties->value)."</pre>";
          break;
        case 'date':
          echo formatAsLongDate($Properties->value);
          break;
        case 'yesno':
          echo formatYesNo($Properties->value);
          break;
      }
      if ($Properties->help_text) {
        echo " <span class=\"detail_help\">".t($Properties->help_text).".</span>";
      }
      echo "</li>";
    } else {
      echo ($Properties->content=='')?"\n":"  <div class=\"separator\">$Properties->content</div>\n";
    }
  }
  echo "</ul>";