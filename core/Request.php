<?php
/**
 * Class that handle Request related things, with a little effort can be used to
 * create from zero and re-create from current request.
 *
 * @package Garson
 * @author Argel Arias <levhita@gmail.com>
 */
class Request {
  /**
   * Singleton instance
   * @var Request
   */
  protected static $__instance = null;
  /**
   * Controller that this request is asking
   * Defaults to {@link IndexController}
   * @var string
   */
  protected $__controller  = 'IndexController';
  /**
   * Action that this request is asking
   * Defaults to IndexAction
   * @var string
   */
  protected $__action      = 'indexAction';
  
  /**
   * The last section of the uri
   * @var string
   */
  protected $__parameters  = '';
  
  /** The full request_uri, after striping the BASE_URL
   * @var string
   */
  protected $__request_uri = '';
  
  protected function __construct() {
    $Config = Config::getInstance();
    $rewrite_base    = $Config->rewrite_base;
    $request_uri = $_SERVER[ 'REQUEST_URI'];
    
    /** Removes everything from the request uri, up to the base url **/
    $rewrite_base_len = strlen($rewrite_base);
    $request_uri_len = strlen($request_uri);
    $x = 0;
    while ( $x<$rewrite_base_len && $x<$request_uri_len && $rewrite_base{$x}==$request_uri{$x} ) {
      $x++;
    }
    $this->__request_uri = trim(substr($request_uri,$x), '/');
    
    /** Explode the elements **/
    $elements = array();
    if ( strlen($this->__request_uri) > 0 ) {
      $elements = explode('/', $this->__request_uri);
    }
      
    /**
     * Acceptable Runtime examples, anything that doesn't conforms to this is
     * unsupported and might get mixed results.
     * PD: ?parameters isn't trustworthy, use $Request->parameter instead
     *
     * controller/action/?parameters
     * 0 controller
     * 1 action
     * 2 ?parameters
     * count 3
     *
     * controller/action
     * 0 controller
     * 1 action
     * count 2
     *
     * controller/?parameters
     * 0 controller
     * 1 ?parameters
     * count 2
     *
     * controller
     * 0 controller
     * count 1
     *
     * ?parameters
     * 0 ?parameters
     * count 1
     *
     * ''
     * 0 ''
     * count 1
     */
    
    //Logger::log('elements', print_r($elements,1));
    switch ( count($elements) ) {
      case 3:
        $this->__controller = ucfirst($elements[0]) . "Controller";
        $this->__action     = strtolower($elements[1]) . "Action";
        $this->__parameters = $elements[2];
        break;
      case 2:
        $this->__controller = ucfirst($elements[0]) . "Controller";
        if (substr($elements[1],0, 1) == '?' ){
          $this->__parameters = $elements[1];
        } else {
          $this->__action = strtolower($elements[1]) . "Action";
        }
        break;
      case 1:
        if (substr($elements[0],0, 1) == '?' ){
          $this->__parameters = $elements[0];
        } else {
          $this->__controller = ucfirst($elements[0]) . "Controller";
        }
        break;
    }
  }
  
  /**
   * Get a single instance of the class (Singleton)
   * @return Request
   */
  public static function getInstance() {
    if (!self::$__instance instanceof self) {
      self::$__instance = new self;
    }
    return self::$__instance;
  }
  
  public function getParams() {
    return $_GET;
  }
  
  public function __get($field)
  {
    return $_GET[$field];
  }
  
  public function __isset($field) {
    return isset($_GET[$field]);
  }
  
  public function getController(){
    return $this->__controller;
  }
  
  public function getAction(){
    return $this->__action;
  }
  
  public function getRequestUri(){
    return $this->_request_uri;
  }
}