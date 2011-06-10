<?php
 $Config= Config::getInstance();
 global $xajax;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= "$Config->system_name -".t($__page_name) ?></title>
    
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold' rel='stylesheet' type='text/css'/>
    
    <link rel="stylesheet" href="<?php $Helper->createFrameLink('style/style.css');?>" type="text/css" media="screen"/>
    <link rel="stylesheet" href="<?php $Helper->createFrameLink('style/print.css');?>" type="text/css" media="print"/>
    
    <link type="text/css" href="<?php $Helper->createFrameLink('vendors/jqueryui/css/redmond/jquery-ui-1.8.13.custom.css');?>" rel="Stylesheet" />	
	<script type="text/javascript" src="<?php $Helper->createFrameLink('vendors/jqueryui/js/jquery-1.5.1.min.js');?>"></script>
	<script type="text/javascript" src="<?php $Helper->createFrameLink('vendors/jqueryui/js/jquery-ui-1.8.13.custom.min.js');?>"></script>
	<script type="text/javascript" src="<?php $Helper->createFrameLink('vendors/jqueryui/js/jquery-ui-timepicker-addon.js');?>"></script>
    
    <script type="text/javascript" src="<?= TO_ROOT ?>/includes/functions.js"></script>
    <script type="text/javascript" src="<?= TO_ROOT ?>/f/includes/functions.js"></script>
    
    <script type="text/javascript" src="<?= TO_ROOT ?>/includes/config.js"></script>
    <script type="text/javascript" src="<?= TO_ROOT ?>/f/includes/config.js"></script>	
    
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
    <div id="overlay"></div>
    
    
    <div id="page">
      <?php if ( isset($__message) ) {
        $Message=(object)$__message;
        ?>
        <div id="message">
          <img id="level_image" src="<?php $Helper->createFrameLink("images/dialogs/$Message->level.png");?>" alt="<?=t(ucwords($Message->level))?>"/>
          <?=$Message->text;?>
        </div>
      <?php } ?>
      
      <div id="header">
      	<h1><a href="<?= $Config->system_web_root ?>" title="<?=t('Home')?>"> <?=$Config->system_name ?></a></h1>
      </div><!-- header -->
	  
	  <div id="top_menu">
        <?php $Helper->loadSubTemplate($__main_menu_template, TRUE); ?>
      </div>
        
      <div id="sidebar">
        <?php $Helper->loadSubTemplate($__secondary_menu_template, TRUE); ?>
      </div><!-- sidebar -->
      
      <div id="content">
        <h2 id="page_title"><?= t($__page_name) ?></h2>
        <?=$_content_ ?>
      </div><!-- content -->
      
      <div id="footer">
      <?= $Config->system_copyright ?><br/>
      <?= $Config->system_author ?><br/>
      
      <?php /** Do NOT remove this Notice!! **/ ?>
      Made with <a href="http://thasystems.net/thaframe">ThaFrame</a>,
      a <a href="http://en.wikipedia.org/wiki/Free_software">Free Software</a>
      development from <a href="http://thasystems.net">ThaSystems</a>.
      <?php /** Of course you are welcome to give it a more apropiated format **/ ?>
      <?php foreach(Logger::getWebLog() as $log ): ?>
       <pre><?=print_r($log,1)?></pre>
      <?php endforeach; ?>
      </div><!-- footer -->
    </div><!-- page --> 
  </body>
</html>
