<?php
  /**
   * Holds general use functions
   * @package ThaFrame
   * @author Argel Arias <levhita@gmail.com>
   * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
   * @license http://opensource.org/licenses/gpl-license.php GNU Public License
   */

  require_once(THAFRAME."/core/format_functions.inc.php");
    

  
  /**
   * loads an Error Page.
   *
   * @param string $page
   * @todo make it posible to load a php file with the original url as parameter.
   */
  function loadErrorPage($page){
    if(file_exists(TO_ROOT ."/pages/$page.html")){
      header("Location: ". SYSTEM_WEB_ROOT . "/pages/$page.html");
    } else {
      header('Content-Type: text/html; charset=UTF-8');
      readfile(THAFRAME . "/pages/$page.html");
    }
    die();
  }
  
  function strleft($s1, $s2)
  {
    return substr($s1, 0, strpos($s1, $s2));
  }
  
  function t(){
    global $_translation;
    if ( func_num_args()<= 0 ) {
      throw new LengthException('Translation function expects at least one parameter.');
    }
    
    $original = (string)func_get_arg(0);
    $search = array();
    $replace = array();
    
    for ($i = 1;$i < func_num_args();$i++) {
      $search[]   = "%$i%";
      $replace[]  = func_get_arg($i);
    }
    if ( array_key_exists($original, $_translation) ){
      $original = $_translation[$original];
    }
    return str_replace($search, $replace, $original);
  }
  
?>
