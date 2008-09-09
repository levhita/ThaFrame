<?php
  global $DbConnection, $xajax;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo SYSTEM_NAME." - $Data->page_name"; ?></title>
    <link rel="stylesheet" href="<?php $Helper->createFrameLink('style/style.css');?>" type="text/css" media="screen"/>
    <!-- <link rel="stylesheet" href="<?php echo TO_ROOT ?>/style/print.css" type="text/css" media="print"/> -->
    <script type="text/javascript" src="<?php echo TO_ROOT ?>/includes/functions.js"></script>
    <script type="text/javascript" src="<?php echo TO_ROOT ?>/f/includes/functions.js"></script>
    <script type="text/javascript" src="<?php echo TO_ROOT ?>/f/vendors/overlib/overlib.js"></script>
    <?php
      if ( isset($xajax) ) {
        $xajax->printJavascript(TO_ROOT.'/f/vendors/xajax/');
      }
      foreach($Data->javascripts as $javascript)
      {
        echo "<script type=\"text/javascript\">$javascript</script>";
      }
    ?>
  </head>
  <body <?php
  if ( isset($Data->__on_load) ) {
      echo "onload=\"{$Data->__on_load}\"";
  }
  ?> >
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
    
    <div id="page">
      <?php if ( isset($Data->__message) ) {
        $Message=(object)$Data->__message;
        ?>
        <div id="message">
        <img class="level_image" src="<?php $Helper->createFrameLink("images/dialogs/$Message->level.png");?>" alt="<? echo $Message->level ?>"/>
        <a href="javascript:void(0);" onclick="javascript:hideMessage();" class="cancel_button" title="Cerrar Mensaje"><img src="<?php $Helper->createFrameLink("images/toolbars/delete.png");?>" alt="delete"/></a>
          <?php echo $Message->text; ?>
        </div>
      <?php } ?>
      <div id="header">
        <h1><a href="<?php echo SYSTEM_WEB_ROOT?>" title="Inicio"><?php echo SYSTEM_NAME; ?></a></h1>
        <div id="top_menu">
          <?php if (isset($_SESSION['user_id']) ) { ?>
            <a href="<?php echo TO_ROOT ?>/ocra/index.php" title="Inicio">Inicio</a> ||
            <a href="new_bid.php" title="Crea una nueva postura">Nueva Postura</a> ||
            <a href="list_bids.php" title="Lista todas las posturas">Posturas</a> ||
            <a href="list_bids_multiple.php" title="Lista todas las posturas multiples">Multiples</a> ||
            <a href="reports.php" title="Muestra la página de reportes">Reportes</a> ||
            <a href="list_items.php" title="Muestra todo los items a rematar">Items</a> ||
            <a href="<?php echo TO_ROOT ?>/account/logout.php">Terminar Sesión</a>
          <?php } else { ?>
            <a href="<?php echo SYSTEM_WEB_ROOT ?>" title="Inicio">Inicio</a> ||
            <a href="<?php echo TO_ROOT ?>/account/login.php" title="Inicio de sesion">Iniciar sesion</a>
          <?php } ?>
        </div>
      
      </div>
      
      <!--<div id="sidebar">
      <h3>Menu Lateral</h3>
      <?php
        if( isset($_SESSION['user_id']) ) {
          echo "session iniciada";
        } else {
          echo "Inicio sesión";
        }
        ?>
     </div>
     -->
      <div id="content">
        <h2 id="page_title"><?php echo $Data->page_name; ?></h2>
