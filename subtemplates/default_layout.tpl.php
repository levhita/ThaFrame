<?php
 $Config= Config::getInstance();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= "$Config->system_name -".t($__page_name) ?></title>
    <link rel="stylesheet" href="<?php $Helper->createFrameLink('style/style.css');?>" type="text/css" media="screen"/>
    <link rel="stylesheet" href="<?php $Helper->createFrameLink('style/print.css');?>" type="text/css" media="print"/>
    <script type="text/javascript" src="<?= TO_ROOT ?>/includes/functions.js"></script>
    <script type="text/javascript" src="<?= TO_ROOT ?>/f/includes/functions.js"></script>
    <script type="text/javascript" src="<?= TO_ROOT ?>/f/vendors/overlib/overlib.js"></script>
    <?php
      if ( isset($xajax) ) {
        $xajax->printJavascript(TO_ROOT.'/f/vendors/xajax/');
      }
      foreach($__javascripts as $javascript)
      {
        echo "<script type=\"text/javascript\">$javascript</script>";
      }
    ?>
  </head>
  <body <?php
  if ( isset($__on_load) ) {
      echo "onload=\"{$__on_load}\"";
  }
  ?> >
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
    
    <div id="page">
      <?php if ( isset($__message) ) {
        $Message=(object)$__message;
        ?>
        <div id="message">
          <img class="level_image" src="<?php $Helper->createFrameLink("images/dialogs/$Message->level.png");?>" alt="<? echo $Message->level ?>"/>
          <a href="javascript:void(0);" onclick="javascript:hideMessage();" class="cancel_button" title="Cerrar Mensaje"><img src="<?php $Helper->createFrameLink("images/toolbars/delete.png");?>" alt="delete"/></a>
          <?php echo $Message->text; ?>
        </div>
      <?php } ?>
      
      <div id="header">
        <h1><a href="<?= $Config->system_web_root ?>" title="<?=t('Home')?>"> <?=$Config->system_name ?></a></h1>
        <div id="top_menu">
          <?php $Helper->loadSubTemplate($__main_menu_template, TRUE); ?>
        </div>
      </div>
      
      <div id="sidebar">
        <?php $Helper->loadSubTemplate($__secondary_menu_template, TRUE); ?>
      </div>
      
      <div id="content">
        <h2 id="page_title"><?= t($__page_name) ?></h2>
        <?=$_content_ ?>
      </div>
      
      <div id="footer">
      <?= $Config->system_copyright ?><br/>
      <?= $Config->system_author ?><br/>
      
      <?php /** Do NOT remove this Notice!! **/ ?>
      Made with <a href="http://thasystems.net/thaframe">ThaFrame</a>,
      a <a href="http://en.wikipedia.org/wiki/Free_software">Free Software</a>
      development from <a href="http://thasystems.net">ThaSystems</a>.
      <?php /** Of course you are welcome to give it a more apropiated format **/ ?>
      </div>
    </div> 
  </body>
</html>
