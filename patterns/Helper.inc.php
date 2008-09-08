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
  public function loadSubTemplate($template)
  {
    $Data   = $this->Data;
    $Helper = $this;
    
    if ( file_exists(TO_ROOT . "/subtemplates/$template.tpl.php") ) {
      include TO_ROOT . "/subtemplates/$template.tpl.php";
    } else {
      if ( file_exists( THAFRAME . "/subtemplates/$template.tpl.php") ) {
        include THAFRAME . "/subtemplates/$template.tpl.php";
      } else {
        throw new Exception("Couldn't find template '$template'");
      }
    }
  }
  
  public function createFrameLink($filename, $return_string=FALSE)
  {
    $string = '';
    if( file_exists(TO_ROOT . "/$filename") ){
      $string .= TO_ROOT . "/$filename";
    } else {
      $string .= TO_ROOT . "/f/$filename";
    }
    if ($return_string){
      return $string;
    }
    echo $string;
  }
  
  public function createActionCall($action, $title, $field, $value, $icon='',$is_ajax=FALSE ,$return_string=FALSE)
  {
    $string = "";
    if( !$is_ajax ) {
      $string .= "<a href=\"javascript:void(xajax_$action($value))\"
      title=\"$title\"><img src=\"". $this->createFrameLink($icon, TRUE) . "\"
      alt=\"$title\"/> $title</a>";
    } else {
      $string .= "<a href=\"$action?$field=$value\"
      title=\"$title\"><img src=\"". $this->createFrameLink($icon, TRUE) . "\"
      alt=\"$title\"/> $title</a>";
    }
    
    if ($return_string){
      return $string;
    }
    echo $string;
  }
}