<?php
class Session
{
  
  /**
   * Holds the logged in user, usually {@link UserModel} or some child class
   * @var  UserModel
   */
  protected static $_User;
  
  protected static $_error;
  
  protected function __construct()
  {
      
  }
     
  public static function setAsLoggedIn($user_id)
  {
    $_SESSION['user_id'] = $user_id; 
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
  }
  
  public static function assertLoggedIn($user_id='')
  {
    if ( !isset($_SESSION['user_id']) ) {
      self::$_error = 'User isn\'t Logged In' ;
    }
    if ( !empty($user_id) ){
      if ( $_SESSION['user_id'] != $user_id) {
        self::$_error = 'User Id doesn\'t Match';
        return false;
      } 
    }
    if ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) {
      self::$_error = 'Ip Changed!';
      return false;
    }
    if ($_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT'] ) {
      self::$_error = 'User Agent Changed!';
      return false;
    }
    return true;
  }
  
  public static function deleteSession() {
    $_SESSION = array();
    session_destroy();
    session_start();
  }

  public static function getUser($class='') {
  	$Config = Config::getInstance();
    if ( !isset(self::$_User) ) {
      if ( !empty($_SESSION['user_id']) ){
        if (empty($class)) {
          $class = (!isset($Config->user_class))?'UserModel':$Config->user_class;
        }
        if ( $class =='UserModel' ) { 
          $User = new UserModel( $Config->user_table, (int)$_SESSION['user_id'] );
        } else {
          $User = new $class( (int)$_SESSION['user_id'] );
        }
        
        if ( !$User->load() ) {
          print_r($_SESSION['user_id']);
          self::$_error = "Couldn't Load User";
          
          return false;
        }
        if ( !$User->loadPermissions() ){
          self::$_error="Couldn't Load Permissions";
          return false;
        }
        self::$_User = $User;
      } else {
        self::$_error = "Not Logged In";
        return false;
      }
  	}
  	return self::$_User;  
  }
  
  public static function getErrorString() {
    return self::$_error;
  }
}