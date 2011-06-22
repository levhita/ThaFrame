<?php
  $Vars = $Data->PatternVariables;
  
  if ($Vars->before_text) {
    echo "<p>".t($Vars->before_text)."</p>\n";
  }
    
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
<div id="calendar"></div>