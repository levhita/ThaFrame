<?php
  global $DbConnection, $xajax, $User;
  header('Content-Type: text/html; charset=utf-8');
   $useragent=$_SERVER['HTTP_USER_AGENT'];
  if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent,0,4))) {
    $style='mobile';
    $media="only screen and (max-device-width: 480px)";
 	$__menu_level=(isset($_GET['level']))?$_GET['level']:3;
  } else {
   $style='style';
   $media="screen";
   $__menu_level=0;
  }
  //Cambiar aquí y la linea anterior variable $style='mobile' para probar mobile
 // $__menu_level=(isset($_GET['level']))?$_GET['level']:3;
  
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" /> <!-- @todo Considerar esta opcion content="width=320"   -->
    <title><?php echo SYSTEM_NAME." - ".t($Data->page_name); ?></title>
    <meta name="application-name" content="Tlalpujahua"/>
    <meta name="description" content="Actualizaciones, Descargas y Administraci√≥n General de Usuarios de OCRA"/>
    <meta name="application-url" content="http://tlalpujahua.ocraocc.org"/>
    <link rel="icon" href="<?php echo TO_ROOT."/style/logo32.png"; ?>" sizes="32x32"/>
    <link rel="icon" href="<?php echo TO_ROOT."/style/logo48.png"; ?>" sizes="48x48"/>
    <link rel="shortcut icon" href="<?php echo TO_ROOT."/style/logo32.png"; ?>"/>
    <link rel="icon" type="image/png" href="<?php echo TO_ROOT."/style/logo32.png"; ?>"/>
    
    <link rel="stylesheet" href="<?php $Helper->createFrameLink('style/'.$style.'.css');?>" type="text/css" media="<?= $media ?>"/>
    <link rel="stylesheet" href="<?php $Helper->createFrameLink('style/print.css');?>" type="text/css" media="print"/>
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Tangerine|Inconsolata|Droid+Sans|Philosopher" />
    
    <script type="text/javascript" src="<?php echo TO_ROOT ?>/includes/functions.js"></script>
    <script type="text/javascript" src="<?php echo TO_ROOT ?>/f/includes/functions.js"></script>
    <!-- <script type="text/javascript" src="<?php echo TO_ROOT ?>/f/vendors/overlib/overlib.js"></script> -->
    <script type="text/javascript" src="<?php echo TO_ROOT ?>/f/vendors/jquery/jquery.js"></script>
    <script type="text/javascript" src="<?php echo TO_ROOT ?>/f/vendors/jquery/simpletip.js"></script>
    <!--[if IE]>
      <link rel="stylesheet" href="<?php $Helper->createFrameLink('style/ie_style_hacks.css');?>" type="text/css" media="screen"/>
    <![endif]-->
    <?php
      if ( isset($xajax) ) {
        $xajax->printJavascript(TO_ROOT.'/f/vendors/xajax/');
      }
      foreach($Data->javascripts as $javascript) {
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
    
    <div id="floating_div" class="hidden"></div>  
    <div id="megashadow" class="hidden"></div> 
    
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
      
      <div id="header" >
      	<div class="logo hide_on_mobile"><a href="<?php echo SYSTEM_WEB_ROOT?>" title="Inicio"><img src="<?php echo TO_ROOT ?>/style/images/logo.png" alt="Inicio"/></a></div>
      	<ul id="top_menu"  class="<?Php echo ($__menu_level!=1)?'hide_on_mobile':'';?>" >
          <?php $Helper->loadSubTemplate($Data->main_menu_template, TRUE); ?>
        </ul> 
      </div>
      <div id="header_mobile" class="hide_on_screen">
      	<div id="back" class="hide_on_screen">
	      	<div id="back_start"></div><div id="back_content" >
	      		<a id="back_mobile" href="javascript:void(history.back())" title="volver">Atrás</a>
	      	</div><div id="back_end"></div>
        </div>
      </div>
      
      <div id="print_header">
		<h1>Tlalpujahua - <?php echo t($Data->page_name); ?></h1>
      </div>
      <div id="body">
      
      <div id="page_title">
    		
    		<?php if (isset($User) ) {?>
	        <div id="user_link">
		        <a  href="<?= TO_ROOT ?>/sections/admin/user/view_user.php?user_id=<?= $User->getId(); ?>">Bienvenido <?= $User->data['name']; ?></a>
		        | <a  href="<?php echo TO_ROOT ?>/account/logout.php" title="Terminar Sesión">Salir</a>
	        </div>
	        <?php } ?><h1><?php echo t($Data->page_name); ?></h1>
      </div>
      
      <?php if ( $Data->secondary_menu_template!='vacio' ) { ?>
        <div id="sidebar" class="<?Php echo ($__menu_level!=2)?'hide_on_mobile':'';?>">
          <?php $Helper->loadSubTemplate($Data->secondary_menu_template, TRUE); ?>
         	<!--<div class="clearBox hide_on_mobile"></div>-->
        </div>
      <?php } ?>
      
     <div class="clearBox hide_on_screen"></div>
        <div id="content" class="<?Php echo ($__menu_level!=3)?'hide_on_mobile':'';?>">
       
          <div id="content_header"><div class="left"></div><div class="right"></div></div>
        <div id="content_body">
        <?=$content?>
        
        </div><!-- end content_body -->
        <div id="content_footer"><div class="left"></div><div class="right"></div></div>
      </div><!-- End content -->
       <div class="separator hide_on_screen"></div>
      </div><!-- End body -->
     
       <div id="footer_mobile" class="hide_on_screen">
        
      </div>
      
      <div id="footer" class="hide_on_mobile">
        <?php echo SYSTEM_COPYRIGHT ?><br/>
        <?php echo SYSTEM_AUTHOR ?><br/>
        <?php echo SYSTEM_DESIGN ?><br/>
        
        <?php /** Do NOT remove this Notice!! **/ ?>
        Made with <a href="http://thasystems.net/thaframe">ThaFrame</a>,
        a <a href="http://en.wikipedia.org/wiki/Free_software">Free Software</a>
        development from <a href="http://thasystems.net">ThaSystems</a>.<br/>
        <?php /** Of course you are welcome to give it a more apropiated format **/ ?>
        <?php
        global $start_time;
        $end_time = microtime_float();
        $time = $end_time - $start_time;
        echo "Page generated in $time seconds";
        ?>
      </div>
    </div>
  </body>
</html>
        