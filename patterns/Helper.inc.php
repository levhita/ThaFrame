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
  
  public function loadSubTemplate($template)
  {
    $Data   = $this->Data;
    $Helper = $this;
    
    if ( file_exists(TO_ROOT . "/subtemplates/$template.tpl.php") ) {
      include TO_ROOT . "/subtemplates/$template.tpl.php";
    } else {
      if ( file_exists(THAFRAME . "/subtemplates/$template.tpl.php") ) {
        include THAFRAME . "/subtemplates/$template.tpl.php";
      } else {
        throw new Exception("Couldn't find template '$template'");
      }
    }
  }
  
  public function createFrameLink($filename)
  {
    if( file_exists(TO_ROOT . "/$filename") ){
      echo TO_ROOT . "/$filename";
    } else {
      echo TO_ROOT . "/f/$filename";
    }
  }
}