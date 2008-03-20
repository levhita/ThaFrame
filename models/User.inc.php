<?php
/**
 * Holds {@link User} model
 * @package ThaFrame
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once TO_ROOT . "/models/Row.inc.php";

/**
 * Provides an user abstraction, basic authentication logic is here too
 * @package ThaFrame
 */
class User extends Row
{
  public function __construct($id, $DbConnection)
  {
    $this->assert_message ="User instance isn't loaded";
    parent::__construct('user', $id, $DbConnection);
  }
  
  public function validatePassword($password)
  {
    $this->assertLoaded();
    
    if ( md5($password) !== $this->data['password'] ) {
      return false;
    }
    
    return true;
  }
  
  public static function assertUniqueName($name)
  {
    global $DbConnection;
    $sql= "SELECT * FROM $this->table_name WHERE name='$name' LIMIT 1";
    if($DbConnection->getOneRow($sql)) {
      return false;
    }
    return true;
  }
}
?>
