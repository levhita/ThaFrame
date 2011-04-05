<?php
/**
 * Holds the {@link DAO} model
 * @package Garson
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2010, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Provides a database abstraction of a Row, simplyfing data access and modification
 * @todo tests!!!
 * @package Garson
 */
class DAO
{
  
  protected $_tableName   = '';
  protected $_idField     = '';
  protected $_id           = 0;
  protected $_data         = array();
  protected $_loaded       = false;

  /**
   * Holds the DbConnection
   *
   * @var DbConnection
   */
  protected $_DbConnection   = null;
  
  /**
   * When the class isn't loaded, an Exception is thrown with this message
   * @var string
   */
  protected $_assert_message = "Class Instance isn't Loaded";
    
  /**
   * Creates DataAccessObject Instance
   * @param string $table_name
   * @param string $id
   * @return DataAccessObject
   */
  public function __construct($tableName, $id) {
    if (!is_string($tableName)) {
      throw new InvalidArgumentException("table_name isn't an string");
    }
    
    if (!is_integer($id)) {
      throw new InvalidArgumentException("id isn't an integer");
    }
    
    $this->_DbConnection = DbConnection::getInstance();
    
    $this->_tableName   = $tableName;
    $this->_id           = $id;
    $this->_idField     = "{$tableName}_id";
  }

  /**
   * Loads the data from the database, remember to load before do anything.
   * @return boolean
   */
  public function load()
  {
    if ( $this->_id != 0 ) {
      $sql = "SELECT *
              FROM $this->_tableName
              WHERE $this->_idField=$this->_id;";
      if ( !$this->_data = $this->_DbConnection->getRow($sql) ) {
        return false;
      }
      $this->_loaded = true;
      return true;
    }
    return false;
  }
  
  /**
   * Setter
   * @param string $field
   * @param string $value
   * @return NULL
   */
  public function __set($field, $value)
  {
    $this->_data[$field] = $value;
  }
  
  /**
   * Getter
   * @param string $field
   * @return string
   */
  public function __get($field)
  {
    return $this->_data[$field];
  }
  
  /**
   * Checks if the given index is setted
   * @param string $field
   * @return boolean
   */
  public function __isset($field){
    return isset($this->_data[$field]);
  }
  
  /**
   * Returns all the data of the row in a convenient array
   * @return array
   */
  public function getAllData()
  {
    return $this->_data;
  }
  
  /**
   * Gets Database Structure as returned by MySQL
   * @return array
   */
  public function getStructure() {
    $structure = $this->_DbConnection->getAllRows("DESCRIBE $this->_tableName;");
    return $structure;
  }
    
  public function getId(){
    return $this->_id;
  }
  
  public function setIdField($field){
    $this->_idField = $field;
  }
  
  /**
   * Saves the data into the database, checking whether is a new row or an old
   * one that just needs an update
   * @return boolean
   */
  public function save()
  {
    if ( !$this->_id ) {
      $fields = array_keys($this->_data);
      $fieldsString = implode(', ', $fields);
      
      $values = array_values($this->_data);
      $aux = array();
      foreach($values as $value)
      {
        $aux[] = "'" . mysql_escape_string($value) . "'";
      }
      $values = implode(', ', $aux);
      
      $sql = "INSERT INTO
              {$this->_tableName}($fieldsString)
              VALUES($values);";
      try{
        $this->_DbConnection->execute($sql);
      }catch(RuntimeException $Exception) {
        Logger::log('Couln\'t save Row', $Exception->getMessage(), 'error');
        return false;
      }
      $this->_id = $this->_DbConnection->getLastId();
      $this->_data[$this->_idField] = $this->_id;
    } else {
      $fieldsStrings = array();
      foreach($this->_data as $field => $value)
      {
        $fieldsStrings[] = "$field='" . mysql_escape_string($value) . "'";
      }
      $fieldString = implode(', ', $fieldsStrings);
      
      $sql = "UPDATE $this->_tableName
              SET $fieldString
              WHERE $this->_idField=$this->_id
              LIMIT 1;";
      if ( !$this->_DbConnection->execute($sql) ) {
        return false;
      }
    }
    if ( !$this->_loaded ){
      $this->_loaded=true;
    }
    return true;
  }
 
  /**
   * Deletes the row on the database, if it violates referential-integrity as
   * defined at database level it will simply return false constraints.
   * @return boolean
   */
  public function delete()
  {
    $sql = "DELETE FROM $this->_tableName
            WHERE $this->_idField=$this->_id
            LIMIT 1;";
    if ( !$this->_DbConnection->execute($sql) ) {
      return false;
    }
    return true;
  }
  
  /**
   * Returns if the class instance is loaded
   * @return boolean
   */
  public function isLoaded()
  {
    return $this->_loaded;
  }
  
  /**
   * Asserts that the DataAccessObject is Loaded
   * @return boolean
   */
  protected function assertLoaded()
  {
    if ( !$this->isLoaded() ) {
      throw new RunTimeException($this->_assert_message);
    }
    return true;
  }
}