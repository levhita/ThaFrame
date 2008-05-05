<?php
/**
 * Holds {@link Edit} class
 * @author Argel Arias <levhita@gmail.com>
 * @package ThaFrame
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once THAFRAME . "/patterns/Page.inc.php";
require_once THAFRAME . "/models/Row.inc.php";

/**
* Provides a {@link Page} that shows a form to edit a {@link Row}
 *
 * @package ThaFrame
 */
class Edit extends Page
{
  /**
   * This is the Row to be edited
   * @var Row
   */
  private $Row    = null;
   
  /**
   * Holds the field's configuration structure
   *
   * fields['name'] {
   *   + label: The label. 
   *   + value: The default value.
   *   + help:  Little text to be show next to the field.
   *   + error_message: If set this message will be show in red below the field.
   *   + type: text, hidden, radio, select, textarea, date
   *   + parameters {}: type specific parameter.
   *   + input_parameters {}: Auto parsed input parameters.
   *   + validation: Function to be applied as validation, the function must get a
   *               string and return true for success or false for invalid input.
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
  
  /**
   * Holds dependents information
   * @var array
   */
  private $dependents = array();
  
  /**
  * Holds conditions information
   * @var array
   */
  private $conditions = array();
  
  /**
   * Construct a {@link Edit} page
   * @param string $page_name the page name to be shown
   * @param string $template by default it uses Edit.tpl.php 
   * @return Edit
   */
  public function __construct($page_name, $template='')
  {
    if ( empty($template) ) {
      $this->setTemplate(THAFRAME . '/patterns/templates/Edit.tpl.php', true);
    } else {
      $this->setTemplate( $template);
    }
    $this->assign('page_name', $page_name);
  }
  
  /**
   * Set the raw data that will be show.
   *
   * Field names to be used as labels are extracted and formatted in this
   * phase, they can of course be overrride using {@link setName}
   * @param  array $rows the raw data 
   * @return void
   */
  public function setRow(Row $Row) {
    $this->Row = $Row;
    $this->no_fields = 0;
    
    /** Parse table structure into template friendly data **/
    $structure = $Row->getStructure();
    foreach($structure AS $field)
    {
      $aux=array();
      $name = $field['Field'];
      $aux['label'] = ucwords(str_replace('_', ' ', $name));
      $aux['value'] = $Row->data[$name];
      
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
            $aux['input_parameters']['maxlength'] = $match[2];
          } else {
            $aux['type'] = 'textarea';
            $aux['input_parameters']['cols'] = '60';
            $aux['input_parameters']['rows'] = '3';
          }
          break;
        case 'char':
          if ( $match[2] <= 100 ) {
            $aux['type'] = 'text';
            $aux['input_parameters']['maxlength'] = $match[2];
          } else {
            $aux['type'] = 'textarea';
            $aux['input_parameters']['cols'] = '60';
            $aux['input_parameters']['rows'] = '3';
          }
          break;
        case 'text':
          $aux['type'] = 'textarea';
          $aux['input_parameters']['cols'] = '60';
          $aux['input_parameters']['rows'] = '6';
          break;
        case 'int':
          $aux['type'] = 'text';
          $aux['input_parameters']['maxlength'] = $match[2];
          break;
        case 'date':
          $aux['type'] = 'date';
          $aux['parameters']['before'] = '5';
          $aux['parameters']['after'] = '5';
          break;
        case 'enum':
        case 'set'://Testing
          if ($match[2] == "'0','1'") {
            $options = array('0'=>'No', '1'=>'Sí');
          } else {
            /** Retrive and parse Options **/
            $options = array();
            $params  = explode("','", $match[2]);
            $params[0] = substr($params[0], 1); //remove the first quote
            $params[ count($params)-1 ] = substr($params[count($params)-1], 0, -1);//remove the second quote
            $options=array_combine($params, $params);//creates a createCombox compatible array
          }
          $aux['type'] = 'select';
          if ( count($options)<=3 ) {
            $aux['type'] = 'radio';
          }
          $aux['parameters']['options']= $options;
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
   * Set as dependent of certain field condition a set of fields.
   * @param string $field The field wich they depend.
   * @param string $condition JavaScript valid condition.
   * @param string $value The value that must match(javascript).
   * @param string $dependents Comma separated list of fields that depend on
   *                           this field value.
   * @return bool true on success and false otherwise.
   */
  public function setFieldDependents($field, $condition, $value, $dependents)
  {
    $aux = array();
    $aux['condition'] = $condition;
    $aux['value']     = $value;
    
    /** Transverse comma separated values into an Array **/ 
    $aux['dependents'] = array_reverse( array_map('trim', explode(',', $dependents) ) );
    
    /** Locate the dependents after their parent field **/
    foreach ( $aux['dependents'] AS $dependent )
    {
      if( !$this->moveAfter($dependent, $field) ){
        return false;
      }
      $this->setFieldProperty($dependent, 'dependent', true);
    }
    
    $this->setFieldProperty($field, 'parent', true);
    
    $this->dependents[$field]['all_fields'] = array_unique(array_merge((array)$this->dependents[$field]['all_fields'] , $aux['dependents']));
    $this->dependents[$field]['conditions'][] = $aux;
    
    return true;
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
  private function insertField($field_name, $field_data, $target, $position='after')
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
   * Inserts a separator (with optional content) at the given position.
   * @param string $target The field after the separator will be created
   * @param string $content The content that will be inside the separator
   * @param string $position 'after' or 'before', Default: 'after'
   * @return bool true on success false otherwise
   */
  public function insertSeparator($target, $content='', $position='after')
  {
    $aux= array('type' => 'separator', 'content' => $content);
    return $this->insertField('', $aux, $target, $position);
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
   * Sets the given field's parameter
   * @param string $field
   * @param string $parameter
   * @param mixed $value 
   * @return bool true on success false otherwise 
   */
  public function setFieldParameter($field, $parameter, $value)
  {
    if ( isset($this->fields[$field]) ) {
      $this->fields[$field]['parameters'][$parameter] = $value;
      return true;
    }
    return false;
  }
  
  /**
   * Sets the given field's input parameter
   * @param string $field
   * @param string $input_parameter
   * @param mixed $value 
   * @return bool true on success false otherwise 
   */
  public function setFieldInputParameter($field, $input_parameter, $value)
  {
    if ( isset($this->fields[$field]) ) {
      $this->fields[$field]['input_parameters'][$input_parameter] = $value;
      return true;
    }
    return false;
  }
  
    
  /**
   * Unsets the given field's input parameter
   * @param string $field
   * @param string $input_parameter
   * @return void 
   */
  public function unsetFieldInputParameter($field, $input_parameter)
  {
    unset($this->fields[$field]['input_parameters'][$input_parameter]);
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
   * Sets the field as hidden
   *
   * Commonly used with the row id.
   * To really delete the field from the Form use {@link deleteField}.
   * @param string $field the name of the field to hide
   * @return bool true on success false otherwise
   */
  public function hideField($field)
  {
    return $this->setFieldProperty($field, 'type', 'hidden');
  }
  
  /**
   * Sets a field as dependent
   *
   * @param string $field The field that will be set as dependent
   * @return bool true on success false otherwise
   *
  public function setAsDependent($field)
  {
    return $this->setFieldProperty($field, 'dependent', true);
  }*/
  
  /**
   * Deletes a field from the form
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
        'action'  => $action ,
        'title'   => $title,
        'icon'    => $icon,
        'ajax'    => $ajax,
      );
    $this->general_actions[] = $aux;
  }
  
  /**
   * Display the selected template with the given data and customization
   * @return void
   */
  public function display() {
    
    if ( count($this->dependents) ) {
      $this->addJavascript( $this->createDependentJavascript() );
    }
    
    $this->assign('data'      , $this->Row->data);
    $this->assign('dependents', $this->dependents);
    $this->assign('fields'    , $this->fields);
    $this->assign('links'     , $this->links);
    $this->assign('general_actions', $this->general_actions);
   
    parent::display();
  }
  
  /**
   * Creates the javascript that powers the depedent engine
   * @return string the code that should be added to the template using {@link addJavascript()} 
   */
  private function createDependentJavascript()
  {
    $code = false;
    if( count($this->dependents) ) {
      
      $code .= "\n  function updateDependents()\n  {";
      
      foreach($this->dependents as $field => $parameters)
      {
        switch( $this->fields[$field]['type'])
        {
          case 'select':
            $get_value_string = "valSelect(document.forms['main_form'].$field)";
            break;
          case 'radio':
            $get_value_string = "valRadioButton(document.forms['main_form'].$field)";
            break;
          default:
            $get_value_string = "document.forms['main_form'].$field.value";
        }
        
        $code .= "\n    field_value = $get_value_string;\n";
        
        $first_run = true;
        
        foreach ( $parameters['conditions'] AS $condition )
        {
          $Condition = (object)$condition;
          
          $code .= ($first_run)?'    if':' else if';
          $code .= " ( field_value $Condition->condition $Condition->value ) {\n";
          
          $hide_fields = array_diff($parameters['all_fields'], $Condition->dependents);
          foreach ( $hide_fields AS $hide)
          {
             $code .= "      dependent = document.getElementById('{$hide}_dependent');\n"; 
             $code .= "      dependent.style.display = 'none';\n";
          }
          foreach ( $Condition->dependents AS $show)
          {
             $code .= "      dependent = document.getElementById('{$show}_dependent');\n"; 
             $code .= "      dependent.style.display = 'block';\n";
          }
          $code .= "    } ";
          $first_run = false;
        }
        
        $code .= "else {\n";
        foreach ( $parameters['all_fields'] AS $all)
        {
          $code .= "      dependent = document.getElementById('{$all}_dependent');\n"; 
          $code .= "      dependent.style.display = 'none';\n";
        }
        $code .= "    }\n";
      }
      $code .="  }\n";
    }
    return $code;
  }
}