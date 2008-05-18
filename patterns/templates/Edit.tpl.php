<?php
include THAFRAME . "/subtemplates/header.tpl.php";

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
      echo "</p>\n\n<div class=\"dependent\" id=\"{$field}_dependent\" style=\"display:none\">\n  ";
    }
    if ($Properties->parent) {
      $input_parameters .= " onchange=\"updateDependents();\"";
    }
    
    if ($Properties->type == 'separator') {
     echo "</p>\n";
     echo ($Properties->content=='')?"\n":"<div class=\"separator\">$Properties->content</div>";
     echo "<p>\n";
    } elseif ($Properties->type != 'hidden') {
      switch($Properties->type){
        case "date":
          echo "<label for=\"{$field}_year\">$Properties->label:</label> ";
          break;
        case "radio":
          echo "<label>$Properties->label:</label> ";
          break;
        default:
          echo "<label for=\"$field\">$Properties->label:</label> ";
      }
      switch ($Properties->type) {
        case "select":
          echo createComboBox($Properties->parameters['options'], $field, $Properties->value, $input_parameters);
          break;
        case "radio":
          echo createRadioButton($Properties->parameters['options'], $field, $Properties->value, $input_parameters);
          break;
        case "date":
          echo createDateComboBox($Properties->value, $Properties->parameters['before'], $Properties->parameters['after'], $field);
          break;          
        case "textarea":
          echo "<br/>\n<textarea name=\"$field\" id=\"$field\" $input_parameters>$Properties->value</textarea>";
          break;
        default:
          echo "<input type=\"text\" name=\"$field\" id=\"$field\" value=\"$Properties->value\" $input_parameters/>";
          break;
      }
      echo "<br/>\n";
    } else {
      echo "<input type=\"hidden\" name=\"$field\" id=\"$field\" value=\"$Properties->value\" $input_parameters/>";
    }
    if ( $Properties->dependent ) {
      echo "</div>\n\n<p>\n";
    }
  }
  echo "</p>\n</form>\n";
  
  if ( !empty($Data->general_actions) ) {
    echo "<p>";
    foreach ( $Data->general_actions as $action)
    {
      $action = (object)$action;
      if ( !$action->ajax) {
        echo "<a href=\"$action->action\" title=\"$action->title\">";
        if ( !$action->icon ) {
          echo "{$action->title}";
        } else {
          echo "<img src=\"$action->icon\" alt=\"{$action->title}\"/>  {$action->title}";
        }
        echo "</a> ";
      } else {
        echo "<a href=\"javascript:void(xajax_{$action->action}(xajax.getFormValues('main_form')))\" title=\"$action->title\">";
        if ( !$action->icon ) {
          echo "{$action->title}";
        } else {
          echo "<img src=\"$action->icon\" alt=\"{$action->title}\"/> {$action->title}";
        }
        echo "</a> ";
      }
    }
    echo "</p>";
  }
  ?>

<?php if( count($Data->dependents) ) { ?>
  <script type="text/javascript">
    updateDependents();
  </script>
<?php } ?>

<?php include THAFRAME . "/subtemplates/footer.tpl.php"; ?>