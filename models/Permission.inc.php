<?php
/**
 * Holds {@link Permission} model
 * @package ThaFrame
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Provides an user abstraction, basic authentication logic is here too
 * @package ThaFrame
 */
class Permissions {
  
  /**
   * Holds the permission catalog
   * @var array
   */
  private $permissions = array();
  
  private $permission_loaded=false;
  
  /**
   * Holds the name of the permissions catalog table
   * @var string
   */
  private $permissions_catalog_table = "";
  
  private $DbConnection = null;
  
  /**
   * Holds the available actions
   * @var string
   */
  private $available_actions = array();
  
  public function __construct($id, $DbConnection)
  {
    $this->assert_message ="Permission instance isn't loaded";
    $this->available_actions = array('all', 'read', 'write', 'delete', 'none');
    $this->permissions_catalog_table = 'permission_catalog'; 
  }
  
  public function setCatalogTable($permissions_catalog){
    $this->permissions_catalog = $permissions_catalog;
  }
  
  public function loadPermissions(){
    $sql = "SELECT *
            FROM $this->permissions_catalog_table
            WHERE active='1'";
    $this->permissions = $this->DbConnection->getAllRows($sql);
    $this->permissions_loaded=true;
  }
  
  public function getPermissionsCatalog(){
    
  }
  
}