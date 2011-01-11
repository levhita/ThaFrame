<?php
  $Vars = $Data->PatternVariables;
    
    echo "<div id=\"many_to_many_detail\">\n";
    echo $Data->Detail->getAsString();
    echo "</div>";
       
    if ($Vars->before_text) {
      echo "<p>".t($Vars->before_text)."</p>\n";
    }
    
    if ( !empty($Data->general_actions) ) {
      echo "<ul class=\"action\">";
      foreach ( $Data->general_actions as $action)
      {
        $action = (object)$action;
        $action->title = t($action->title);
        echo "<li>";
        if( !empty($action->field) ) {
          if ( strpos($action->action,'?') === FALSE) {
            echo "<a href=\"$action->action?$action->field={$action->value}\" title=\"$action->title\">";
          } else {
            echo "<a href=\"$action->action&$action->field={$action->value}\" title=\"$action->title\">";
          }
        } else {
          echo "<a href=\"$action->action\" title=\"$action->title\">";
        }
        if ( !$action->icon ) {
          echo "{$action->title}";
        } else {
          $action->icon = $Helper->createFrameLink($action->icon, TRUE);
          echo "<img src=\"$action->icon\" alt=\"{$action->title}\"/> {$action->title}";
        }
        echo "</a></li> ";
       }
      echo "</ul>\n";
    }
    
    echo "<div id=\"many_to_many_table\">";
    echo $Data->Table->getAsString();
    echo "</div>";
    
    if ($Vars->after_text) {
      echo "\n<p>$Vars->after_text</p>\n";
    }