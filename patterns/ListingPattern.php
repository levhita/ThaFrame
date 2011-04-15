<?php
/**
 * Holds {@link ListingPattern} class
 * @author Argel Arias <levhita@gmail.com>
 * @package ThaFrame
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */


/**
 * Provides a {@link PagePattern} that shows an item's list.
 *
 * Trough several methods it allows you to add links and actions asociated
 * with each row, as well as formating
 * @package ThaFrame
 * @todo paging , column re-ordering
 */
class ListingPattern extends PagePattern
{
  /**
   * Holds all the raw data that will be listed
   * @var array
   */
  private $_rows    = array();
   
  /**
   * Holds the field names that will form the table header
   * @var array
   */
  private $_fields  = array();
  
  /**
   * Holds the links that will be embedded into some fields
   * @var array
   */
  private $_links   = array();
  
  /**
   * Holds the links that will be added in the last column
   * @var array
   */
  private $_actions = array();
  
  /**
   * Holds the tooltips that will be attached to the fields.
   * @var array
   */
  private $_tooltips = array();
  
  /**
   * Holds the prefix that'll be used to create an unique id for every row
   * @var string
   */
  private $_prefix  = '';
  
  /**
   * Points to the field that will be used to create the unique id
   * @var string
   */
  private $_row_id  = '';
  
  /**
   * Holds actions that will be rendered at begining or/and end of the list
   * actions that belong to the paga, and not to a specific row
   * @var array
   */
  private $_general_actions   = array();
  
  /**
   * Holds filter options and configuration
   * @var array
   */
  private $_filters   = array();
  
  /**
   * Holds classes to be applied to the columns
   * @var array
   */
  private $_classes   = array();
  
  /**
   * Stores if the page should be paginated
   *
   * @var boolean
   */
  private $_paginate = false;
  
  /**
   * Holds the number of elements for each page
   *
   * @var integer
   */
  private $_page_size = 50;
  
  /**
   * Which page to show
   *
   * @var integer
   */
  private $_page_number = 0;
  
  /**
   * Number of pages
   *
   * @var integer
   */
  private $_pages = 0;
  
  /**
   * Wich is the final SQL query to be called
   *
   * @var string
   */
  private $_sql = '';
  
  /**
   * The conditions string that is generated after take in account the applied filters
   *
   * @var string
   */
  private $_conditions = '';
  
  /**
   * Construct a {@link Listing} page
   * @param string $page_name the page name to be shown
   * @param string $template by default it uses Listing.tpl.php
   * @return Listing
   */
  public function __construct($page_name='', $template='', $layout='')
  {
    if ( empty($template) ) {
      $this->setTemplate(THAFRAME . '/patterns/templates/ListingPattern.tpl.php', true);
    } else {
      $this->setTemplate($template);
    }
    $this->setPageName($page_name);
    $this->setLayout($layout);
  }
  
  /**
   * Set the raw data that will be show.
   *
   * Field names to be used as table headers are extracted and formatted in this
   * phase, they can of course be overrride using {@link setName}
   * @param  array $rows the raw data
   * @return void
   */
  public function setRows($rows) {
    $this->_rows = $rows;
    if ( $rows ) {
      $fields_names = array_keys($rows[0]);
      foreach($fields_names AS $field_name)
      {
        $this->_fields[$field_name] = ucwords(str_replace('_', ' ',$field_name));
      }
    }
  }
  
  /**
   * Names each row with an unique id.
   *
   * It's formed with the prefix + the given field's value. Used to provide
   * named items that can be referred easily with {@link xajax} and javascript
   * @param string $prefix the prefix
   * @param string $field the field
   * @return void
   */
  public function setRowId($prefix, $field )
  {
    $this->_prefix = $prefix;
    $this->_row_id = $field;
  }
  
  /**
   * Sets the name of a field as will be show in the table header.
   *
   * If not customized this name is created by replacing underscores with espaces
   * and capitalizing each word in the field name.
   * @param string $field the field where the name will be changed
   * @param string $name the new name
   * @return void
   */
  public function setName($field, $name)
  {
    if ( isset($this->_fields[$field]) ) {
      $this->_fields[$field] = $name;
    }
  }
  
  /**
   * Adds an action link embedded into the given field.
   * @param string $field The field that will have the embedded link (ie name).
   * @param string $value The field that will serve as the value (ie item_id).
   * @param string $action The action to be performed (aka URL).
   * @param string $title An optional title for the link.
   * @return void
   */
  public function addLink($field, $value, $action, $title='' ) {
    $aux = array (
        'value'   => $value ,
        'action'  => $action ,
        'title'   => $title
      );
    $this->_links[$field] = $aux;
  }
  
  /**
   * Adds an action link at the end of the row
   * @param string $value The field that will serve as the value (ie item_id)
   * @param string $action The action to be performed, can be xajax or an URL
   * @param string $title The title of the link
   * @param string $icon An optional icon, if not provided a regular link is created
   * @param bool   $ajax Tells if the action is xajax or a regular URL
   * @return void
   * @todo create a multiple parameter action creator
   */
  public function addAction($value, $action, $title, $icon='', $ajax=false)
  {
    $aux = array (
        'value'   => $value ,
        'action'  => $action ,
        'title'   => $title,
        'icon'    => $icon,
        'ajax'    => $ajax,
      );
    if(strpos($value,',')!==false){
      $single_values = explode(',', $value);
      $values = array();
      foreach($single_values AS $single_value){
        $values[]=trim($single_value);
      }
      $aux['value'] = $values;
    }
    $this->_actions[] = $aux;
  }
  
  /**
   * Adds a tooltip to a field
   * 
   * @param string $field the field that'll have the tooltip attached
   * @param string $text Can be someting as "tooltip: %field%", where field can
   * be any field in the query.
   * @todo Make this work
   * @return void
   */
  public function addToolTip($field, $text) {
    $text = t($text);
    preg_match_all("/\%([A-Za-z0-9_\-]+)\%/", $text, $matches);
    $fields = $matches[1];
    $aux = array (
      'text' => $text,
      'fields' => $fields,     
    );
    $this->_tooltips[$field] = $aux;
    $javascript = <<<EOT
    $(document).ready(function(){
      $(".tooltip_$field").each(function(i){
        $(this).simpletip({
          boundryCheck: true,  
          fixed: true,
          position: 'left',
		  showEffect: "", /*slide, fade*/
		  hideEffect: "",
          content: $(this).attr('title')

        });
      });
    });
EOT;
//{ $("JQUERY SELECTOR").each(function(i){
 //  $(this).simpletip({ content: arrayData[i] });
//});
        //bodyHandler: function() { 
        //  return $($(this).attr("title")).html(); 
        //}
    $this->addJavascript($javascript);
  }
  
  /**
   * Avoid the given field to be show.
   *
   * Commonly used to hide the table id, but still allow its use in
   * {@link addAction()} or {@link addLink()} as $value.
   * @param string $field the name of the field to hide
   * @return void
   */
  public function hideField($field) {
    unset( $this->_fields[$field] );
  }
  
  /**
   * Add an action to the end & start of the Listing, commonly used to add a
   * "Create new item" link
   *
   * @param string $action The action that will be called after clicking (url)
   * @param string $title The text to show and will be added to the url title as well
   * @param string $field The field to add into de URL
   * @param string $value The value that such field should take usally 0 for new elements
   * @param string $icon  Tn optional icon that could go with the text
   * @return void
   */
  public function addGeneralAction($action, $title, $field='', $value='', $icon='', $ajax=false)
  {
    $aux = array (
        'action'  => $action ,
        'title'   => $title,
        'field'   => $field ,
        'value'   => $value ,
        'icon'    => $icon,
    	'ajax'    => $ajax,
      );
    $this->_general_actions[] = $aux;
  }
  
  /**
   * Adds a filter form to the list
   * @param $field
   * @param $label
   * @param $type
   * @return bool
   */
  public function addFilter($field, $label, $type='custom')
  {
    $aux = array (
        'label' => $label,
        'type'  => $type,
        'empty' => $empty,
        'options' => array()
    );
    $this->_filters[$field] = $aux;
  }
  
  public function addHiddenFilter($field, $value, $condition){
    $aux = array (
        'type'  => 'hidden',
        'value' => $value,
        'condition' => $condition
    );
    $this->_filters[$field] = $aux;
  }
  
  /**
   * Adds a filter option
   * @param $field
   * @param $value
   * @param $label
   * @param $default
   * @param $condition
   * @return bool
   */
  public function addFilterOption($field, $value, $label, $default=FALSE, $condition='')
  {
    $aux = array (
        'label' => $label,
        'value' => $value,
        'condition' => $condition,
    );
    if($default) {
      $this->_filters[$field]['default']= $value;
    }
    $this->_filters[$field]['options'][] = $aux;
  }
  
  public function setClass($field, $value) {
    $this->_classes[$field] = $value; 
  }
  
  public function addFilterOptions($field, $values, $condition){
    if (is_array($values) ) {
      foreach($values as $value=>$label) {
        $search  = array('{value}', '{label}');
        $replace = array(mysql_escape_string($value), mysql_escape_string($label));
        $replaced_condition = str_replace($search, $replace, $condition);
        $this->addFilterOption($field, $value, $label, FALSE, $replaced_condition);
      }
    }
  }
  
  public function setQuery($sql, DbConnection $DbConnection=null, $paginate = false) {
    if (is_null($DbConnection) ) {
      $DbConnection = DbConnection::getInstance();
    }
    
    if ($paginate) {
      $this->_paginate = true;
      
      //Get a Grip of the whole thing
      $sql_without_conditions = str_replace('{conditions}','',$sql);
      $count_sql = "SELECT count(*) FROM ($sql_without_conditions) AS count_table;";
      $total_rows = $DbConnection->getOneValue($count_sql);
      
      //Create some basic pattern variables
      $this->_page_number = (empty($_GET['__page_number']))?'0':$_GET['__page_number'];
      $this->_page_size = (empty($_GET['__page_size']))?$this->_page_size:$_GET['__page_size'];
      $this->_pages = ceil($total_rows/$this->_page_size);
      
      if($this->_page_number > $this->_pages){
        $this->_page_number = $this->_pages-1;
      }
      
      //Reformat the query to use MySQL Limit clause
      $page_start = $this->_page_number * $this->_page_size;
      $sql = "$sql
            LIMIT $page_start, $this->_page_size";
      
      $this->setPatternVariable('paginate', $this->_paginate);
      $this->setPatternVariable('page_number', $this->_page_number);
      $this->setPatternVariable('page_size',$this->_page_size);
      $this->setPatternVariable('pages', $this->_pages);
    }
    
    $conditions = '';
    if ( count($this->_filters) ) {
      
      foreach($this->_filters AS $field => $filter) {
        $Filter = (object)$filter;
        if($Filter->type=='custom') {
          //echo "Checking for filter on '$field'\n";
          if( isset($_GET[$field]) ) {
            $selected = stripslashes($_GET[$field]);
          } else {
            $selected = $this->_filters[$field]['default'];
          }
          //echo "Selected:$selected\n";
          foreach($Filter->options AS $option){
            //echo "Comparing value: {$option['value']}\n";
            if ( $option['value'] == $selected) {
                //echo "Match!, condition added '{$option['condition']}'\n\n";
                $conditions .= "\nAND ";
                $conditions .= $option['condition'];
                $this->_filters[$field]['selected']= $selected;
            }
          }
        } else if($Filter->type=='hidden') {
          $conditions .= "\nAND ";
          $conditions .= $Filter->condition;
        }
      }
    }

    if ( empty($conditions) ) {
      $sql = str_replace('{conditions}','',$sql);
    } else {
      $sql = str_replace('{conditions}', $conditions, $sql);
    }
    
    $this->_conditions = $conditions;
    $this->_sql        = $sql;
    
    $rows = $DbConnection->getAllRows($sql);
    $this->setRows($rows);
  }
  
  public function setFormat($field, $function)
  {
    if( function_exists($function) && is_array($this->_rows) ) {
      for($i=0; $i<count($this->_rows); $i++)
      {
        $this->_rows[$i][$field]=$function($this->_rows[$i][$field]);
      }
    }
  }
  
  /**
   * Display the selected template with the given data and customization
   * @return void
   */
  public function display() {
    $this->assign('__rows'    , $this->_rows);
    $this->assign('__fields'  , $this->_fields);
    $this->assign('__links'   , $this->_links);
    $this->assign('__actions' , $this->_actions);
    $this->assign('__classes' , $this->_classes);
    $this->assign('__tooltips' , $this->_tooltips);
    $this->assign('__prefix'  , $this->_prefix);
    $this->assign('__row_id'  , $this->_row_id);
    $this->assign('__filters' , $this->_filters);
    $this->assign('__general_actions' , $this->_general_actions);

    parent::display();
  }
  
  public function getConditions() {
    return $this->_conditions;
  }
  
  public function getQuery(){
    return $this->_sql;
  }
}
?>
