<?php
/**
 * Holds {@link UserModel}
 * @package ThaFrame
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Provides an user abstraction, basic authentication logic is here too
 * @package ThaFrame
 */
class UserModel extends RowModel
{
  /**
   * Holds the permission for this user in the name => actions(0=> 'action1', 1=>'action2') format
   * @var array
   */
  protected $permissions = array();
  
  protected $permissions_loaded = FALSE;
  
  public function __construct($table_name, $id)
  {
    $this->assert_message ="User instance isn't loaded";
    parent::__construct($table_name, $id, $DbConnection);
  }
  
  public function validatePassword($password)
  {
    $this->assertLoaded();
    
    if ( sha1($password) !== $this->data['password'] ) {
      return false;
    }
    
    return true;
  }
  
  public static function assertUniqueName($name)
  {
    $DbConnection = DbConnection::getInstance();
    $Config = Config::getInstance();
    $sql= "SELECT * FROM $Config->user_table WHERE name='$name' LIMIT 1";
    if($DbConnection->getOneRow($sql)) {
      return false;
    }
    return true;
  }
  
  public static function getUserByName($name)
  {
    $DbConnection = DbConnection::getInstance();
    $Config = Config::getInstance();
    $sql= "SELECT $Config->user_table_id FROM $Config->user_table WHERE name='$name' LIMIT 1";
    if(!$user_id = $DbConnection->getOneRow($sql)) {
      return false;
    }
    $User = new UserModel($Config->user_table,(int)$user_id);
    $User->load();
    return  $User;
  }
  
  public function loadPermissions()
  {
    $Config = Config::getInstance();
    $this->assertLoaded();
    $sql = "SELECT name, actions
            FROM permission
            WHERE $this->id_field='$this->id'
            AND active='1'";
    
    if(!$permissions = $this->DbConnection->getArrayPair($sql)){
      $permissions = array();
    }
     
    $processed_permissions=array();
    foreach($permissions AS $object => $actions_string)
    {
      $actions =  explode(',', $actions_string);
      $processed_actions = array();
      foreach($actions AS $action){
        $processed_actions[]=trim($action);
      }
      $processed_permissions[$object] = $processed_actions;
    }
    $this->permissions = $processed_permissions;
    $this->permissions_loaded = TRUE;
    return TRUE;
  }
  
  /**
   * Throws an exception if permissions aren't loaded
   */
  public function assertPermissionsLoaded()
  {
    if( !$this->permissions_loaded ){
      throw new BadMethodCallException("User permissions aren't loaded");
    }
  }
  
  /**
   * Checks if the user has permission on to realize the given
   * action on the given object.
   *
   * @param string $object
   * @param string $desired_action
   * @return TRUE if he has it, FALSE otherwise.
   */
  public function hasPermission($object, $desired_action)
  {
    $this->assertPermissionsLoaded();
    
    $object = strtolower($object);
    $desired_action = strtolower($desired_action);
    while(!empty($object) ){
      if ( array_key_exists($object, $this->permissions) ){
        $actions = $this->permissions[$object];
        if ( array_search('all', $actions)!== FALSE ) {
          if ( array_search("-$desired_action", $actions)!== FALSE ) {
            return FALSE;//Has an 'all' but the desired_action has an exception
          }
          return TRUE;//Has an 'all' without this specific exception
        } else {
          if ( array_search('none', $actions)!== FALSE ) {
            if ( array_search("+$desired_action", $actions)!== FALSE ) {
              return TRUE;//Has a 'none' but the desired_action has an exception
            }
            return FALSE; //Has a 'none' without any exception
          } else {
            if ( array_search($desired_action, $actions)!== FALSE ) {
              return TRUE;//Has this action
            }
            return FALSE;//Doesn't have this action
          }
        }
      }
      /**
       * In case of total inexistance of an '/' erase from the beginning
       * Otherwise we cut to the next '/' including it.
       *
       * Be careful! in this code we use alot of string behavior of PHP
       * strlen returns the length of the string and strrpos returns position
       * starting from '0' we need to correct this +1 difference in
       * interpretations.
       */
      $object = substr($object, 0, -1);
      $position = (strrpos($object, '/')===FALSE)? 0 : strrpos($object, '/');
      $position = ($position==0)? 1 : $position;
      $object = substr($object, 0, $position);
    }
    /** Empty string and still we didn't find any permission on that object **/
    return FALSE;
  }
}
?>
