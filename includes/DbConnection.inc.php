<?php
/**
 * Holds {@link DbConnection} class
 * @package ThaFrame
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
  * Database Connection abstraction
  *
  * Provides extremely useful functions for data retrieval, and other database
  * affairs.
  * @package ThaFrame
  */
class DbConnection {
  
  private $db_connection  = null;
  private $db_host     = '';
  private $db_user     = '';
  private $db_password = '';
  private $db_name     = '';
  private $errors      = array();
  
  
  
  public function __construct($db_host, $db_user, $db_password, $db_name)
  {
    $this->db_host     = $db_host;
    $this->db_user     = $db_user;
    $this->db_password = $db_password;
    $this->db_name     = $db_name;
  }
  
  public function connect()
  {
    if ( !$this->db_connection = @mysql_connect($this->db_host, $this->db_user, $this->db_password) ) {
      throw new RunTimeException("Couldn't connect to the database server");
    }
    if ( !@mysql_select_db($this->db_name, $this->db_connection) ) {
      throw new RunTimeException("Couldn't connect to the given database");
    }
  }
  
  public function getAllRows($sql)
  {
    if ( !$results = @mysql_query($sql, $this->db_connection) ) {
      throw new RunTimeException("Couldn't execute query: ". mysql_error($this->db_connection) );
    }
    
    $count = 0;
    $rows  = array();
    while ( $row = mysql_fetch_assoc($results) ) {
      $rows[] = $row;
      $count++;
    }
    return ($count)?$rows:false;
  }
  
  public function getOneColumn($sql)
  {
    if ( !$results = @mysql_query($sql, $this->db_connection) ) {
      throw new RunTimeException("Couldn't execute query: ". mysql_error($this->db_connection) );
    }
    
    $count = 0;
    $rows  = array();
    while ( $row = mysql_fetch_array($results) ) {
      $rows[] = $row[0];
      $count++;
    }
    return ($count)?$rows:false;
  }
  
  public function getArrayPair($sql)
  {
    if ( !$results = @mysql_query($sql, $this->db_connection) ) {
      throw new RunTimeException("Couldn't execute query: ". mysql_error($this->db_connection) );
    }
    
    $count = 0;
    $rows  = array();
    while ( $row = mysql_fetch_array($results) ) {
      $rows[$row[0]] = $row[1];
      $count++;
    }
    return ($count)?$rows:false;
  }
  
  public function getOneRow($sql)
  {
    if ( !$results = @mysql_query($sql, $this->db_connection) ) {
      throw new RunTimeException("Couldn't execute query: ". mysql_error($this->db_connection) );
    }
    
    if ( $row = mysql_fetch_assoc($results) ) {
      return $row;
    }
    return false;
  }
  
  public function getOneValue($sql)
  {
    if ( !$results = @mysql_query($sql, $this->db_connection) ) {
      throw new RunTimeException("Couldn't execute query: ". mysql_error($this->db_connection) );
    }
    
    if ( $row = mysql_fetch_array($results) ) {
      return $row[0];
    }
    return false;
  }
  
  public function executeQuery($sql)
  {
    if ( !@mysql_query($sql, $this->db_connection) ) {
      $this->errors[] = mysql_error($this->db_connection);
      return false;
    }
    return true;
  }
  
  public function getErrors()
  {
    return $this->errors;
  }
  public function getErrorsString(){
    $string="";
    foreach($this->errors AS $error){
      $string .= "$error\n";
    }
    return $string;
  }
  public function getLastId()
  {
    return mysql_insert_id($this->db_connection);
  }
  
  public function getMysqlConnection() {
    return $this->db_connection;
  }
}
?>
