<a class="action_right" title="Delete" href="javascript:void(0)"
  onclick="javascript:xajax_inactiveTask('<?php echo $Data->task_id ?>')"><img src="images/delete.png" alt="delete"/></a>
<a class="action_right" title="Edit" href="javascript:void(0)"
  onclick="javascript:xajax_createTaskEditForm('<?php echo $Data->task_id ?>')"><img src="images/edit.png" alt="edit"/></a>
  
<?php if ($Data->description) { ?>
  <a class="action_right" onmouseover="return overlib('".<?php echo str_replace("\n", '<br/>', $Data->description) ?>"');"
    onmouseout="return nd();\" href="javascript:void(0)"><img src="images/comment.png" alt="Description"/></a>
<?php } ?>

<img src="images/prio<?php echo $Data->priority ?>.png" alt="Priority <?php echo $Data->priority ?>"/>
<a title="Mark" href="javascript:void(0);" onclick="javascript:xajax_switchStatus(<?php echo $Data->task_id ?>);">
  <img id="task_checkbox_<?php echo $Data->task_id ?>"
  <?php echo ($Data->done=='1')?'src="images/done.png" alt="done"':'src="images/pending.png" alt="pending"' ?>/>
</a>

<span class=\"task_name\">
  <?php echo formatAsDate($Data->added, 'short')?> : <?php echo  $Data->name ?>
</span><br/>

<span class=\"smalltext\">
  <?php
    if ( $Data->project_id ) {
      echo "[<a href=\"javascript:void(0);\"
      onclick=\"javascript:xajax_loadProjectTasks($Data->project_id);\">$this->project_name</a>]";
    } else {
      echo " <span style=\"color:red\">[personal]</span> ";
    }
  ?>
  <?php
    if ( $Data->user_id ) {
      echo " &lt;$this->user_name&gt;";
    } else {
      echo " <span style=\"color:red\">&lt;¡unassigned!&gt;</span> ";
    }
  ?>
</span>
