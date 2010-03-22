<?php
/**
 * Helper class that holds methods that help in the templates
 * @package ThaFrame
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2008, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

class Helper {
  
  /*
   * Data from the Page;
   * @var object
   */
  protected $Data;
  
  public function __construct(&$Data)
  {
    $this->Data = $Data;
  }
  
  /*
   * Load a subtemplate, selectiong between a custom template or a standard
   * framework subtemplate
   */
  public function loadSubTemplate($template, $fullpath=FALSE)
  {
    $Data   = $this->Data;
    $Helper = $this;
    if (!$fullpath) {
      if ( file_exists(TO_ROOT . "/subtemplates/$template.tpl.php") ) {
        include TO_ROOT . "/subtemplates/$template.tpl.php";
      } else {
        if ( file_exists( THAFRAME . "/subtemplates/$template.tpl.php") ) {
          include THAFRAME . "/subtemplates/$template.tpl.php";
        } else {
          throw new Exception("Couldn't find template '$template'");
        }
      }
    } else {
      if ( file_exists( $template) ) {
        include $template;
      } else {
        throw new Exception("Couldn't find template '$template'");
      }
    }
  }
  
  public function createFrameLink($filename, $return_string=FALSE, $absolute_path = false)
  {
    $string = '';
    if ($absolute_path){
        $string .= SYSTEM_WEB_ROOT . "/$filename";
    } else {
      if( file_exists(TO_ROOT . "/$filename") ){
        $string .= TO_ROOT . "/$filename";
      } else {
        $string .= TO_ROOT . "/f/$filename";
      }
    }
    
    if ($return_string){
      return $string;
    }
    echo $string;
  }
  
  public function createSelfUrl($parameters, $return_string=FALSE) {
    global $web_root;
    $string .= $web_root. $_SERVER['PHP_SELF'];
    $original_parameters = $_GET;
    $parameters = array_merge($original_parameters, $parameters);
    if ( count($parameters) ) {
      $first=TRUE;
      foreach($parameters as $variable=>$value) {
        $string .=($first)?'?':'&';
        $string .= urlencode($variable) . "=" . urlencode($value);
        $first = FALSE;
      }
    }
    if ($return_string) {
      return $string;
    }
    echo $string;
  }
  
  public function createActionCall($action, $title, $field='', $value='', $icon='', $is_ajax=FALSE , $return_string=FALSE)
  {
    $string = "";
    $title = t($title);
    if( $is_ajax ) {
      $string .= "<a href=\"javascript:void(xajax_$action($value))\" title=\"$title\">";
    } else {
      if ( !empty($field) ) {
        $string .= "<a href=\"$action?$field=$value\" title=\"$title\">";
      } else {
        $string .= "<a href=\"$action\" title=\"$title\">";
      }
    }
    if ( !empty($icon) ) {
      $icon =  $this->createFrameLink($icon, TRUE);
      $string .= "<img src=\"$icon\" alt=\"$title\"/>";
    }
    $string .= "$title</a>";
    
    if ($return_string){
      return $string;
    }
    echo $string;
  }
  
  public function objetizeArray($array){
    $clean_array = array();
    foreach($array AS $key => $value){
      $clean_array[$key] = htmlspecialchars($value);
    }
    return (object)$clean_array;
  }
}
