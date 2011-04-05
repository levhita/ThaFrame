<?php
  $Vars = $Data->PatternVariables;
  
  if ($Vars->before_text) {
    echo "<p>".t($Vars->before_text)."</p>\n";
  }
  echo "<form action=\"$Data->target\" id=\"main_form\">\n";
  echo "<p>\n";
  
  foreach($Data->fields as $field=>$properties){
    $Properties = (object) $properties;
    $input_parameters = "";
    if ( count($Properties->input_parameters) ) {
      foreach($Properties->input_parameters AS $property => $value)
      {
        $input_parameters .= " $property=\"$value\"";
      }
    }
    if ( $Properties->dependent ) {
      echo "</p>\n\n<div class=\"dependent\" id=\"{$field}_dependent\" style=\"display:none\">\n";
    }
    if ($Properties->parent) {
      $input_parameters .= " onchange=\"updateDependents();\"";
    }
    
    if ($Properties->type == 'separator') {
      if ( $Properties->dependent ) {
        echo ($Properties->content=='')?"\n":"  <div class=\"separator\">$Properties->content</div>\n";
      } else {
        echo ($Properties->content=='')?"\n":"</p>\n\n<div class=\"separator\">$Properties->content</div>\n\n<p>\n";
      }
    } elseif ($Properties->type != 'hidden') {
      switch($Properties->type){//For PreLabels
        case "date":
          echo "<label for=\"{$field}_year\">".t($Properties->label).":</label> ";
          break;
        case "radio":
          echo "<label>".t($Properties->label).":</label> ";
          break;
        default:
          echo "<label for=\"$field\">".t($Properties->label).":</label> ";
      }
      if ($Properties->help_text){
        switch($Properties->type){//For help text following the label
          case "textarea":
            echo "<span class=\"input_help\">".t($Properties->help_text).".</span>";
            break;
        }
      }
      $readonly = ($Properties->disabled == 'true')?'readonly="readonly"':'';
      switch ($Properties->type) {
        case "select":
          if ( !empty($readonly)) {
            echo $Properties->parameters['options'][$Properties->value];
            echo "<input type=\"hidden\" name=\"$field\" id=\"$field\" value=\"".htmlspecialchars($Properties->value)."\" $input_parameters/>";
          } else {
            echo createComboBox($Properties->parameters['options'], $field, $Properties->value, $input_parameters);
          }
          break;
        case "radio":
          if ( !empty($readonly) ) {
            echo htmlspecialchars($Properties->parameters['options'][$Properties->value]);
            echo "<input type=\"hidden\" name=\"$field\" id=\"$field\" value=\"".htmlspecialchars($Properties->value)."\" $input_parameters/>";
          } else {
            echo createRadioButton($Properties->parameters['options'], $field, $Properties->value, $input_parameters);
          }
          break;
        case "date":
          echo createDateComboBox($Properties->value, $Properties->parameters['before'], $Properties->parameters['after'], $field);
          break;
        case "textarea":
          echo "<br/>\n<textarea name=\"$field\" id=\"$field\" $input_parameters $readonly>".htmlspecialchars($Properties->value)."</textarea>";
          break;
        case "password":
          echo "<input type=\"password\" name=\"$field\" id=\"$field\" value=\"".htmlspecialchars($Properties->value)."\" $input_parameters $readonly/>";
          if ( $Properties->repeat && empty($readonly)) {
            echo "<br/>\n<label for=\"{$field}_repeat\">" . t('Repeat the %1%', t($Properties->label) ) . ":</label> ";
            echo "<input type=\"password\" name=\"{$field}_repeat\" id=\"{$field}_repeat\" value=\"".htmlspecialchars($Properties->value)."\" $input_parameters/>";
          }
          break;
        default:
          echo "<input type=\"text\" name=\"$field\" id=\"$field\" value=\"".htmlspecialchars($Properties->value)."\" $input_parameters $readonly/>";
          break;
      }
      if ($Properties->help_text){
        switch($Properties->type){//For help text following the field
          case "textarea":
            break;
          default:
            echo " <span class=\"input_help\">".t($Properties->help_text).".</span>";
        }
      }
      echo "<br/>\n";
    } else {
      echo "<input type=\"hidden\" name=\"$field\" id=\"$field\" value=\"".htmlspecialchars($Properties->value)."\" $input_parameters/>";
    }
    if ( $Properties->dependent ) {
      echo "</div>\n\n<p>\n";
    }
  }
  echo "</p>\n</form>\n";
  
  if ( !empty($Data->general_actions) ) {
    echo "<ul class=\"action\">";
    foreach ( $Data->general_actions as $action)
    {
      echo "<li>";
      $action = (object)$action;
      $action->title = t($action->title);
      $action->icon = $Helper->createFrameLink($action->icon, 1);
      if ( !$action->ajax) {
        echo "<a href=\"$action->action\" title=\"$action->title\">";
        if ( !$action->icon ) {
          echo "{$action->title}";
        } else {
          echo "<img src=\"$action->icon\" alt=\"{$action->title}\"/>  {$action->title}";
        }
        echo "</a> ";
      } else {
        echo "<a href=\"javascript:void(xajax_{$action->action}(xajax.getFormValues('main_form')));\" title=\"$action->title\">";
        if ( !$action->icon ) {
          echo "{$action->title}";
        } else {
          echo "<img src=\"$action->icon\" alt=\"{$action->title}\"/> {$action->title}";
        }
        echo "</a> ";
      }
      echo "</li>";
    }
    echo "</ul>";
  }
  ?>

<?php if( count($Data->dependents) ) { ?>
  <script type="text/javascript">
    updateDependents();
  </script>
<?php } ?>