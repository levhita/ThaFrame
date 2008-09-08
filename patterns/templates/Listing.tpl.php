<?php
  $Helper->loadSubTemplate('header');

    $Vars = $Data->PatternVariables;
    if ($Vars->before_text) {
      echo "<p>$Vars->before_text</p>\n";
    }
    if ( !empty($Data->general_actions) ) {
      echo "<ul class=\"action\">";
      foreach ( $Data->general_actions as $action)
      {
        $action = (object)$action;
        echo "<li>";
        if ( strpos($action->action,'?') === FALSE) {
          echo "<a href=\"$action->action?$action->field={$action->value}\" title=\"$action->title\">";
        } else {
          echo "<a href=\"$action->action&$action->field={$action->value}\" title=\"$action->title\">";
        }
        if ( !$action->icon ) {
          echo "{$action->title}";
        } else {
          echo "<img src=\"$action->icon\" alt=\"{$action->title}\"/> {$action->title}";
        }
        echo "</a></li> ";
       }
      echo "</ul>\n";
    }
    
    if ( $Data->rows ) {
      echo "\n<table>\n";
      echo "<tr>";
      foreach($Data->fields as $field_title)
      {
        echo "<th>$field_title</th>";
      }
      if ( count($Data->actions) ) {
        echo "<th>Actions</th>";
      }
      echo "</tr>\n";
      $count=0;
      foreach($Data->rows AS $row)
      {
        if ( ($count % 2) == 1 ){
          $class="odd";
        } else {
          $class="even";
        }
        echo "<tr class=\"$class\"";
        if($Data->prefix)
          echo " id=\"{$Data->prefix}_{$row[$Data->row_id]}\" ";
        echo ">";
        $count++;
        foreach($Data->fields as $field => $field_title)
        {
          
          if( isset($Data->links[$field]) ) {
            $link = (object)$Data->links[$field];
            if(strpos($link->action,'?') === FALSE) {
              echo "<td><a href=\"$link->action?$link->value={$row[$link->value]}\" title=\"$link->title\">{$row[$field]}</a></td>";
            } else {
              echo "<td><a href=\"$link->action&$link->value={$row[$link->value]}\" title=\"$link->title\">{$row[$field]}</a></td>";
            }
          } else {
            echo "<td>{$row[$field]}</td>";
          }
          
        }
        if ( !empty($Data->actions) ) {
          echo "<td>";
          foreach ( $Data->actions as $action)
          {
            $action = (object)$action;
            if ( !$action->ajax) {
              if ( strpos($action->action,'?') === FALSE) {
                echo "<a href=\"$action->action?$action->value={$row[$action->value]}\" title=\"$action->title\">";
              } else {
                echo "<a href=\"$action->action&$action->value={$row[$action->value]}\" title=\"$action->title\">";
              }
              if ( !$action->icon ) {
                echo "{$action->title}";
              } else {
                echo "<img src=\"$action->icon\" alt=\"{$action->title}\"/>";
              }
              echo "</a> ";
            } else {
              echo "<a href=\"javascript:void(xajax_{$action->action}({$row[$action->value]}));\" title=\"$action->title\">";
              if ( !$action->icon ) {
                echo "{$action->title}";
              } else {
                echo "<img src=\"$action->icon\" alt=\"{$action->title}\"/>";
              }
              echo "</a> ";
            }
          }
          echo "</td>";
        }
        echo "</tr>\n";
      }
      echo "</table>\n";
    } else {
      if ($Vars->no_items_message) {
        echo "\n<p><strong>$Vars->no_items_message</strong></p>\n";
      } else {
        echo "\n<p><strong>There are no items</strong\n</p>";
      }
    }
    if ($Vars->after_text) {
      echo "\n<p>$Vars->after_text</p>\n";
    }
  $Helper->loadSubTemplate('footer');