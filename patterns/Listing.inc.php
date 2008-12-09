<?php
/**
 * Holds {@link Listing} class
 * @author Argel Arias <levhita@gmail.com>
 * @package ThaFrame
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once THAFRAME . "/patterns/Page.inc.php";

/**
 * Provides a {@link Page} that shows an item's list.
 *
 * Trough several methods it allows you to add links and actions asociated
 * with each row, as well as formating
 * @package Freimi
 * @todo paging , column re-ordering
 */
class Listing extends Page
{
  /**
   * Holds all the raw data that will be listed
   * @var array
   */
  private $rows    = array();
   
  /**
   * Holds the field names that will form the table header
   * @var array
   */
  private $fields  = array();
  
  /**
   * Holds the links that will be embedded into some fields
   * @var array
   */
  private $links   = array();
  
  /**
   * Holds the links that will be added in the last column
   * @var array
   */
  private $actions = array();
  
  /**
   * Holds the prefix that'll be used to create an unique id for every row
   * @var string
   */
  private $prefix  = '';
  
  /**
   * Points to the field that will be used to create the unique id
   * @var string
   */
  private $row_id  = '';
  
  /**
   * Holds actions that will be rendered at begining or/and end of the list
   * actions that belong to the paga, and not to a specific row
   * @var array
   */
  private $general_actions   = array();
  
  /**
   * Stores if the page should be paginated
   *
   * @var boolean
   */
  private $paginate = false;
  
  /**
   * Holds the number of elements for each page
   *
   * @var integer
   */
  private $page_size = 20;
  
  /**
   * Which page to show
   *
   * @var integer
   */
  private $page_number = 0;
  /**
   * Number of pages
   *
   * @var integer
   */
  private $pages = 0;
  
  /**
   * Construct a {@link Listing} page
   * @param string $page_name the page name to be shown
   * @param string $template by default it uses Listing.tpl.php
   * @return Listing
   */
  public function __construct($page_name, $template='')
  {
    if ( empty($template) ) {
      $this->setTemplate(THAFRAME . '/patterns/templates/Listing.tpl.php', true);
    } else {
      $this->setTemplate( $template);
    }
    $this->assign('page_name', $page_name);
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
    $this->rows = $rows;
    if ( $rows ) {
      $fields_names = array_keys($rows[0]);
      foreach($fields_names AS $field_name)
      {
        $this->fields[$field_name] = ucwords(str_replace('_', ' ',$field_name));
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
    $this->prefix = $prefix;
    $this->row_id = $field;
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
    if ( isset($this->fields[$field]) ) {
      $this->fields[$field] = $name;
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
    $this->links[$field] = $aux;
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
    $this->actions[] = $aux;
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
    unset( $this->fields[$field] );
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
  public function addGeneralAction($action, $title, $field='', $value='', $icon='')
  {
    $aux = array (
        'action'  => $action ,
        'title'   => $title,
        'field'   => $field ,
        'value'   => $value ,
        'icon'    => $icon,
      );
    $this->general_actions[] = $aux;
  }
  
 
  /**
   * Display the selected template with the given data and customization
   * @return void
   */
  public function display() {
    $this->assign('rows'    , $this->rows);
    $this->assign('fields'  , $this->fields);
    $this->assign('links'   , $this->links);
    $this->assign('actions' , $this->actions);
    $this->assign('prefix'  , $this->prefix);
    $this->assign('row_id'  , $this->row_id);
    $this->assign('general_actions' , $this->general_actions);

    parent::display();
  }
  
  public function setQuery($sql, DbConnection $DbConnection, $paginate = false) {
    if ($paginate) {
      $this->paginate = true;
      
      //Get a Grip of the whole thing
      $count_sql = "SELECT count(*) FROM ($sql) AS count_table;";
      $total_rows = $DbConnection->getOneValue($count_sql);
      
      //Create some basic pattern variables
      $this->page_number = (empty($_GET['__page_number']))?'0':$_GET['__page_number'];
      $this->page_size = (empty($_GET['__page_size']))?25:$_GET['__page_size'];
      $this->pages = ceil($total_rows/$this->page_size);
      
      if($this->page_number >= $this->pages){
        $this->page_number = $this->pages-1;
      }
      
      //Reformat the query to use MySQL Limit clause
      $page_start = $this->page_number * $this->page_size;
      $sql = "$sql
            LIMIT $page_start, $this->page_size";
      
      $this->setPatternVariable('paginate', $this->paginate);
      $this->setPatternVariable('page_number', $this->page_number);
      $this->setPatternVariable('page_size',$this->page_size);
      $this->setPatternVariable('pages', $this->pages);
    }
    $rows = $DbConnection->getAllRows($sql);
    $this->setRows($rows);
  }
  
  public function setFormat($field, $function)
  {
    if( function_exists($function) ) {
      for($i=0; $i<count($this->rows); $i++)
      {
        $this->rows[$i][$field]=$function($this->rows[$i][$field]);
      }
    }
  }
}
?>
