<?php
/**
 * Holds the {@link Row} model
 * @package ThaFrame
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 * Provides a database abstraction of a Row, simplyfing data access and modification
 * @package ThaFrame
 */
class Row {
  
  protected $table_name   = '';
  protected $id           = 0;
  public $data         = array();
  public $loaded       = false;
  protected $DbConnection = null;
  protected $assert_message ="Class instance isn't loaded";
  
  public function __construct($table_name, $id, $DbConnection) {
    if (!is_string($table_name)) {
      throw new InvalidArgumentException("table_name isn't an string");
    }
    
    if (!is_integer($id)) {
      throw new InvalidArgumentException("id isn't an integer");
    }
    
    if (get_class($DbConnection)!== 'DbConnection') {
      throw new InvalidArgumentException("DbConnection isn't a DbConnection");
    }
    
    $this->table_name   = $table_name;
    $this->table_id     ="id_$table_name";
    $this->id           = $id;
    $this->DbConnection = $DbConnection;
  }
  
  public function load()
  {
    $sql = "SELECT *
            FROM {$this->table_name}
            WHERE {$this->table_id}={$this->id};";
    if ( !$this->data = $this->DbConnection->getOneRow($sql) ) {
      return false;
    }
    $this->loaded = true;
    return true;
  }
  
  public function getStructure() {
    $structure = $this->DbConnection->getAllRows("DESCRIBE $this->table_name;");
    return $structure;
  }
  
  public function save() {
    if ( !$this->id ) {
      $fields = array_keys($this->data);
      $fields_string = implode(', ', $fields);
      
      $values = array_values($this->data);
      $aux = array();
      foreach($values as $value)
      {
        $aux[] = "'" . mysql_real_escape_string($value) . "'";
      }
      $values = implode(', ', $aux);
      
      $sql = "INSERT INTO
              {$this->table_name}($fields_string)
              VALUES($values);"; 
      if ( !$this->DbConnection->executeQuery($sql) ) {
        return false;
      }
      $this->id = $this->DbConnection->getLastId();
      $this->data[$this->table_id] = $this->id;
    } else {
      $fields_strings = array();
      foreach($this->data as $field => $value)
      {
        $fields_strings[] = "$field='" . addslashes($value) . "'"; 
      }
      $field_string = implode(', ', $fields_strings);
      
      $sql = "UPDATE {$this->table_name}
              SET $field_string
              WHERE {$this->table_id}={$this->id}
              LIMIT 1;";
      if ( !$this->DbConnection->executeQuery($sql) ) {
        return false;
      }
    }
    if ( !$this->loaded ){
      $this->loaded=true;
    }
    return true;
  }
  public function inactive(){
    $this->data['active'] = '0';
  }
  
  public function assertLoaded()
  {
    if ( !$this->loaded ) {
      throw new RunTimeException($this->assert_message);
    }
    return true;
  }
  
  public function delete()
  {
    $sql = "DELETE FROM {$this->table_name}
            WHERE {$this->table_id}={$this->id}
            LIMIT 1;";
    if ( !$this->DbConnection->executeQuery($sql) ) {
      return false;
    }
    return true;
  }
}
?>
