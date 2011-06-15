<?php
class XajaxHelper {
  
  private function _construct() {}
  
  /**
   * Utility function to edit a single 
   * @param string $table_name
   * @param int $id
   * @param string $field
   * @param var $value
   * @return True on success, false otherwise.
   */
  public static function editField($table_name, $id, $field, $value){
    $DbConnection = DbConnection::getInstance();
        
    $Row= new Row($table_name, (int)$id, $DbConnection);
    $Row->data[$field]=$value;
    
    return $Row->save();
  }
  
  /**
   * Saves Given data in the given table
   * 
   * Performs basic date type cleaning
   * @param array $data
   * @param string $table_name
   * @return True on success, false otherwise
   */
  public static function saveRow($data, $table_name)
  {
    $DbConnection = DbConnection::getInstance();
    
    $table_id = "{$table_name}_id";
    $id = (int)$data[$table_id];
    $Row = new RowModel($table_name, $id, $DbConnection);
    
    if ( !$Row->load() && $id!=0 ) {
      throw new UnexpectedValueException('Couldn\'t load row, to be saved');
    }
    
    foreach ($data AS $field => $value) {
      $Row->data[$field] = $value;
    }
    
    if ( !$Row->save() ) {
      return false;
    }
    return true;
  }
}