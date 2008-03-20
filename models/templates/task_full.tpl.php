<div id="task_<?php echo $Data->task_id ?>"
  class="task <?php echo ($Data->done=='1')?'done':'pending'; ?>" >
  <?php include TO_ROOT . "/models/templates/task_innerhtml.tpl.php"; ?>
</div>
