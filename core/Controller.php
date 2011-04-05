<?php
/**
 * @package Garson
 * @author Argel Arias <levhita@gmail.com>
 */
class Controller
{
  /**
   * Our Request Object
   * @var Request
   */
  protected $_request = null;
  /**
   * @return Controller
   */
  public function __construct(){
    $this->_request = Request::getInstance();
  }
  
  public function gotoPage($page, $message='') {
    if ( !empty($message) ) {
      $_SESSION['_MESSAGE_'] = _($message);
    }
    header('Location: ' . BASE_URL . $page);
    exit();
  }
}