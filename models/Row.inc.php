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
  protected $id_field     = '';
  protected $id           = 0;
  public    $data         = array();
  protected $loaded       = false;
  /**
   * Holds the DbConnection
   *
   * @var DbConnection
   */
  protected $DbConnection   = null;
  protected $assert_message = "Class instance isn't loaded";
  protected $full_string    = "<div class=\"{%table_name}\" id=\"{%table_name}{%id}\">\n{%content}\n</div>\n\n";
  
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
    $this->id           = $id;
    $this->id_field     = "{$table_name}_id";
    $this->DbConnection = $DbConnection;
  }

  
  public function getStructure() {
    $structure = $this->DbConnection->getAllRows("DESCRIBE $this->table_name;");
    return $structure;
  }
  
  public function getIdField()
  {
   return $this->id_field;
  }
  
  public function setIdField($field)
  {
    $this->id_field = $field;
  }
  
  public function load()
  {
    if ( $this->id != 0 ) {
      $sql = "SELECT *
              FROM $this->table_name
              WHERE $this->id_field=$this->id;";
      if ( !$this->data = $this->DbConnection->getOneRow($sql) ) {
        return false;
      }
      $this->loaded = true;
      return true;
    }
    return false;
  }
  
  public function save()
  {
    if ( !$this->id ) {
      $fields = array_keys($this->data);
      $fields_string = implode(', ', $fields);
      
      $values = array_values($this->data);
      $aux = array();
      foreach($values as $value)
      {
        $aux[] = "'" . mysql_escape_string($value) . "'";
      }
      $values = implode(', ', $aux);
      
      $sql = "INSERT INTO
              {$this->table_name}($fields_string)
              VALUES($values);";
      if ( !$this->DbConnection->executeQuery($sql) ) {
        return false;
      }
      $this->id = $this->DbConnection->getLastId();
      $this->data[$this->id_field] = $this->id;
    } else {
      $fields_strings = array();
      foreach($this->data as $field => $value)
      {
        $fields_strings[] = "$field='" . mysql_escape_string($value) . "'";
      }
      $field_string = implode(', ', $fields_strings);
      
      $sql = "UPDATE $this->table_name
              SET $field_string
              WHERE $this->id_field=$this->id
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
 
  public function inactive()
  {
    $this->data['active'] = '0';
    return TRUE;
  }
  
  public function isLoaded(){
  	return $this->loaded;
  }
  
  public function delete()
  {
    $sql = "DELETE FROM $this->table_name
            WHERE $this->id_field=$this->id
            LIMIT 1;";
    if ( !$this->DbConnection->executeQuery($sql) ) {
      return false;
    }
    return true;
  }
  
  public function innerHTML($template, $show_as_full_string = true , $full_path = false)
  {
    if ( !$full_path ) {
      $template = "models/templates/$template.tpl.php";
    }
    if ( !file_exists($template) ) {
      throw new InvalidArgumentException("Template file '$template' doesn't exists");
    }
    $Data = (object)$this->data;
    
    ob_start();
    include $template;
    $string = ob_get_contents();
    ob_end_clean();
    
    /** Whe use a replace aproach to create the full_string version of the content **/
    if ( $show_as_full_string==true ) {
      /** this are the currently supporte keywords **/
      $search = array('{%id}', '{%table_name}', '{%content}');
      
      /** That are going to be replaced by this **/
      $replace   = array($this->id, $this->table_name, $string);
      $new_string = str_replace($search, $replace, $this->full_string);
      $string = $new_string;
    }
    
    return $string;
  }
  
  public function getId(){
  	return $this->id;
  }

  public function assertLoaded()
  {
    if ( !$this->loaded ) {
      throw new RunTimeException($this->assert_message);
    }
    return true;
  }
}
?>