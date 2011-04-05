<?php
require_once THAFRAME . '/patterns/Template.inc.php';

class Detail Extends Template
{
  /**
   * Holds the Row to be used
   * @var Row
   */
  protected $Row = '';
  
  /**
   * Holds the field's configuration structure
   *
   * fields['name'] {
   *   + label: The label.
   *   + help:  Little text to be show next to the field.
   *   + parameters {}: type specific parameter.
   *   + type: text, separator
   * }
   * @var array
   */
  private $fields  = array();
  
  /**
  * Holds the number of fields that this {@link Edit} has
   * @var int
   */
  private $no_fields = 0;
  
  /**
   * Holds actions that will be rendered at begining or/and end of the form
   * @var array
   */
  private $general_actions = array();
  
  public function __construct($template = '')
  {
    if ( empty($template) ) {
      $this->setTemplate(THAFRAME . '/patterns/templates/Detail.tpl.php', true);
    } else {
      $this->setTemplate($template);
    }
  }
  
  public function setRow(Row $Row){
      $this->Row = $Row;
    $this->no_fields = 0;
    
    /** Parse table structure into template friendly data **/
    $structure = $Row->getStructure();
    foreach($structure AS $field)
    {
      $aux = array();
      $name = $field['Field'];
      $aux['label'] = ucwords(str_replace('_', ' ', $name));
      $aux['value'] = (isset($Row->data[$name]))?$Row->data[$name]:$field['Default'];
         
      $this->no_fields++;
      /**
       * Extract type information.
       * $match[0] The whole string. ie: int(11) unsigned
       * $match[1] The type. ie: int
       * $match[2] The type parameters ie: 11
       * $match[3] Extra. ie: unsigned
       */
      preg_match('/^([a-z]*)(?:\((.*)\))?\s?(.*)$/', $field['Type'], $match);
      switch($match[1]){
        case 'varchar':
          if ( $match[2] <= 100 ) {
            $aux['type'] = 'text';
          } else {
            $aux['type'] = 'textarea';
          }
          break;
        case 'char':
          if ( $match[2] <= 100 ) {
            $aux['type'] = 'text';
          } else {
            $aux['type'] = 'textarea';
          }
          break;
        case 'text':
          $aux['type'] = 'textarea';
          break;
        case 'int':
          $aux['type'] = 'text';
          break;
        case 'date':
          $aux['type'] = 'date';
          break;
        case 'enum':
        case 'set'://Testing
          if ($match[2] == "'0','1'") {
            $aux['type'] = 'yesno';
          } else {
            $aux['type'] = 'text';
          }
          break;
      }
      $this->fields[$name] = $aux;
    }
  }
  
 /**
   * Moves the given field to the start of the form
   * @param string $field the field to be moved
   * @return bool true in success and false otherwise
   */
  public function moveToStart($field)
  {
    if ( isset($this->fields[$field]) ) {
      $aux = array ( $field => $this->fields[$field] );
      unset($this->fields[$field]);
      $this->fields = $aux + $this->fields;
      return true;
    }
    return false;
  }
  
  /**
   * Moves the given field to the end of the form
   * @param string $field the field to be moved
   * @return bool true in success and false otherwise
   */
  public function moveToEnd($field)
  {
    if ( isset($this->fields[$field]) ) {
      $aux = array ( $field => $this->fields[$field] );
      unset($this->fields[$field]);
      $this->fields = $this->fields + $aux;
      return true;
    }
    return false;
  }
  
  /**
   * Moves the given field before another field
   * @param string $field The field to move
   * @param string $before_field The field before the $field will be located
   * @return bool true on success and false otherwise
   */
  public function moveBefore($field, $before_field)
  {
    if ( isset($this->fields[$field]) ) {
      $field_data = $this->fields[$field];
      unset($this->fields[$field]);
      return $this->insertField($field, $field_data, $before_field, 'before');
    }
    return false;
  }
  
  /**
   * Moves the given field before another field
   * @param string $field The field to move
   * @param string $after_field The field after the $field will be located
   * @return bool true on success and false otherwise
   */
  public function moveAfter($field, $after_field)
  {
    if ( isset($this->fields[$field]) ) {
      $field_data = $this->fields[$field];
      unset($this->fields[$field]);
      return $this->insertField($field, $field_data, $after_field, 'after');
    }
    return false;
  }
  
/**
   * Insert a Field after or before the given target
   * @param string $field_name How will be named the field
   * @param array $field_data a complete field array
   * @param string $target The name of the field after or before we'll
   *                       insert the new field.
   * @param string $position 'after' or 'before', Default: 'after'
   * @return bool true on success false otherwise
   */
  public function insertField($field_name, $field_data, $target, $position='after')
  {
    $success = false;
    /** there is no easy way to insert an element into an array, so we need to
    recreate it, inserting the field when we detect the $target **/
    $new_fields = array();
    reset($this->fields);
    while (list($key, $value) = each($this->fields) ) {
      if($position=='after') {
        $new_fields[$key] = $value;
      }
      if ( $key === $target) {
        if ( $field_name!='' ) {
          $new_fields[$field_name] = $field_data;
        } else {
          $new_fields[] = $field_data;
        }
        $success = true;
      }
      if($position=='before') {
        $new_fields[$key] = $value;
      }
    }
    $this->fields = $new_fields;
    
    return $success;
  }
  
  public function setFieldOrder($fields)
  {
    $fields = explode(',', $fields);
    $fields = array_map('trim', $fields);
    if ( count($fields)!=$this->no_fields) {
      throw new LogicException("The number of fields doesn't match the ones in the Row, you are missing some fields");
    }
    $new_fields= array();
    foreach($fields as $field)
    {
      if ( !isset($this->fields[$field]) ) {
        throw new LogicException("The given field '$field' doesn't exist");
      }
      $new_fields[$field] =$this->fields[$field];
    }
    $this->fields = $new_fields;
  }
  
  /**
   * Inserts an splitter (with optional content) at the given position.
   * @param string $target The field after the separator will be created
   * @param string $content The content that will be inside the splitter
   * @param string $position 'after' or 'before', Default: 'after'
   * @return bool true on success false otherwise
   */
  public function insertSplitter($target, $content='', $position='after', $name='')
  {
    $aux= array('type' => 'separator', 'content' => $content);
    return $this->insertField("{$name}_splitter", $aux, $target, $position);
  }
  
  /**
   * Sets the given field's property
   * @param string $field
   * @param string $property
   * @param mixed $value
   * @return bool true on success false otherwise
   */
  public function setFieldProperty($field, $property, $value)
  {
    if ( isset($this->fields[$field]) ) {
      $this->fields[$field][$property] = $value;
      return true;
    }
    return false;
  }
  
/**
   * Sets the name of a field as will be show in the Label
   *
   * If not customized this name is created by replacing underscores with espaces
   * and capitalizing each word in the field name.
   * @param string $field the field where the name will be changed
   * @param string $name the new name
   * @return bool true on success false otherwise
   */
  public function setName($field, $name)
  {
    return $this->setFieldProperty($field, 'label', $name);
  }
  
  /**
   * Sets a help text that will be put besides the field
   *
   * @param string $field the field where the help text will be added
   * @param string $help_text the text
   * @return bool true on success false otherwise
   */
  public function setHelpText($field, $help_text)
  {
    return $this->setFieldProperty($field, 'help_text', $help_text);
  }
  
  /**
   * Deletes a field from the Form
   *
   * If you only wish to hide a field use {@link hideField}
   * @param string $field the name of the field to be deleted
   * @return void
   */
  public function deleteField($field) {
    if ( isset($this->fields[$field]) ) {
      unset( $this->fields[$field] );
      $this->no_fields--;
      return true;
    }
    return false;
  }
  
public function setAsLinked($field, $table_name, $DbConnection, $table_id='', $name_field='')
  {
    if ($table_id=='') {
      $table_id = "{$table_name}_id";
    }
    
    if ($name_field=='') {
      $name_field = NAME_FIELD;
    }
    
    $sql = "SELECT $table_id, $name_field
            FROM $table_name
            ORDER BY $name_field";
    $options = $DbConnection->getArrayPair($sql);
    $this->setFieldProperty($field, 'type', 'select');
    
    $this->unsetFieldInputParameter($field, 'maxlength');
    
    if ( count($options)<=3 ) {
      $this->setFieldProperty($field, 'type', 'radio');
    }
    $this->setFieldParameter($field, 'options', $options);
  }
  
  /**
   * Add an action to the end & start of the Form, commonly used to add a
   * "Delete" link
   *
   * @param string $action The action that will be called after clicking (url)
   * @param string $title The text to show and will be added to the url title as well
   * @param string $field The field to add into de URL
   * @param string $value The value that such field should take usally 0 for new elements
   * @param string $icon  The optional icon that could go with the text
   * @return void
   */
  public function AddGeneralAction($action, $title, $icon='', $ajax=false)
  {
    $aux = array (
        'action'  => $action,
        'title'   => $title,
        'icon'    => $icon,
        'ajax'    => $ajax,
      );
    $this->general_actions[] = $aux;
  }
  

  
  public function getAsString(){
    $this->assign('Row', $this->Row);
    $this->assign('data'      , $this->Row->data);
    $this->assign('fields'    , $this->fields);
    $this->assign('links'     , $this->links);
    $this->assign('general_actions', $this->general_actions);
    return parent::getAsString();
  }
}