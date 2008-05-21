<?php
  $Helper->loadSubTemplate('header');

    $Vars = $Data->PatternVariables;
    if ($Vars->before_text) {
      echo "<p>$Vars->before_text</p>";
    }
    if ( !empty($Data->general_actions) ) {
      echo "<p>";
      foreach ( $Data->general_actions as $action)
      {
        $action = (object)$action;
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
        echo "</a> ";
       }
      echo "</p>";
    }
    
    if ( $Data->rows ) {
      echo "<table>\n";
      echo "<tr>";
      foreach($Data->fields as $field_title)
      {
        echo "<th>$field_title</th>";
      }
      if ( count($Data->actions) ) {
        echo "<th>Acciones</th>";
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
              echo "<a href=\"javascript:void(xajax_{$action->action}({$row[$action->value]}))\" title=\"$action->title\">";
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
        echo "<p><strong>$Vars->no_items_message</strong></p>";
      } else {
        echo "<p><strong>There are no items</strong></p>";
      }
    }
    if ($Vars->after_text) {
      echo "<p>$Vars->after_text</p>";
    }
  $Helper->loadSubTemplate('footer');