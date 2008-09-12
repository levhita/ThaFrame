<?php
require_once THAFRAME . '/patterns/Helper.inc.php';

/**
 * Holds {@link Page} class
 * @package ThaFrame
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 * Provide basic template system and http some http functionality
 * @package ThaFrame
 */
class Page
{
  /**
   * Holds the variables to be passed to the template as $Data object
   * @var array
   */
  protected $variables = array();
  
  /**
   * Holds the javascripts that should be added at the head section
   * @var array
   */
  protected $javascripts = array();
  
  /**
   * Holds the relative path to the template
   * @var string
   */
  protected $template = '';
  
  /**
   * Script to run on page load
   *
   * @var string
   */
  public $on_load   = '';
  
  /**
   * Holds the header to be sent on_display
   *
   * @var array
   */
  public $headers = array();

  /**
   * Variables that belongs only to this pattern, used to customize the text and
   * appareance of the page
   * @var array
   */
  protected $pattern_variables = array();
  /**
   * Holds the main menu template
   * @var string
   */
  public $main_menu_template = '';
  /**
   * Holds the secondary menu template
   * @var string
   */
  public $secondary_menu_template = '';
  
  public function __construct($page_name='', $template='')
  {
    if ( empty($template) ) {
      $template = $this->getScriptName();
    }

    $this->setTemplate($template);
  	$this->assign('page_name', $page_name);
  }
  
  /**
   * Shows the given template
   *
   * Converts the $variables array into $Data object and sets any message that may
   * be in the $_SESSION and finally calls the given template
   * @return void
   */
  public function display()
  {
    if ( !file_exists($this->template) ) {
      throw new InvalidArgumentException("'$this->template' doesn't exists");
    }
    if( isset($_SESSION['__message_text']) ) {
      $message = array(
        'level' => $_SESSION['__message_level'] ,
        'text' => $_SESSION['__message_text']
      );
      
      $this->assign('__message', $message);
      unset($_SESSION['__message_text']);
      unset($_SESSION['__message_level']);
      unset($message);
    }
    $script_name = $this->getScriptName();
    if ( empty($this->main_menu_template) ) {
      if ( file_exists("templates/" . $this->getScriptName() . "_menu.tpl.php") ) {
        $this->setMainMenu($this->getScriptName()."_menu");
      } else if (file_exists('templates/default_main_menu.tpl.php') ) {
        $this->setMainMenu('default_main_menu');
      } else if (file_exists(TO_ROOT. "/subtemplates/default_main_menu.tpl.php") ) {
        $this->setMainMenu(TO_ROOT. '/subtemplates/default_main_menu.tpl.php', TRUE);
      } else {
        $this->setMainMenu(THAFRAME. '/subtemplates/default_main_menu.tpl.php', TRUE);
      }
    }
    
    if ( empty($this->secondary_menu_template) ) {
      if ( file_exists("templates/" . $this->getScriptName() . "_menu.tpl.php") ) {
        $this->setSecondaryMenu($this->getScriptName()."_menu");
      } else if (file_exists('templates/default_secondary_menu.tpl.php') ) {
        $this->setSecondaryMenu('default_secondary_menu');
      } else if (file_exists(TO_ROOT. "/subtemplates/default_secondary_menu.tpl.php") ) {
        $this->setSecondaryMenu(TO_ROOT. '/subtemplates/default_secondary_menu.tpl.php', TRUE);
      } else {
        $this->setSecondaryMenu(THAFRAME. '/subtemplates/default_secondary_menu.tpl.php', TRUE);
      }
    }
    
    $this->assign('PatternVariables', (object)$this->pattern_variables);
    $this->assign('javascripts', $this->javascripts);
    $this->assign('main_menu_template', $this->main_menu_template);
    $this->assign('secondary_menu_template', $this->secondary_menu_template);
    
    /** Convert all variables into an object **/
    $Data = (object)$this->variables;
    
    /** This object actually helps to do a variety of thisn inside templates
     * @var Helper
     */
    $Helper = new Helper($Data);
    
    /** Sends the headers **/
    foreach($this->headers AS $header => $value)
    {
      header("$header: $value");
    }
    
    include $this->template;
  }
  
  /**
   * Adds a javascript that will be added in the head section
   * @param string $javascript the javascript code
   * @return void
   */
  public function addJavascript($javascript)
  {
    $this->javascripts[] = $javascript;
  }
  
  /**
   * Sets the template file to be used.
   *
   * Take for granted that the file is under the relative path "templates/" and
   * has a "tpl.php" extension, unless you set $fullpath to true
   * @param string  $template The name of the template to be used
   * @param bool    $fullpath overrides the naming convention and allows you to set any file
   * @return void
   */
  public function setTemplate($template, $fullpath = false)
  {
    if ( !$fullpath) {
      $this->template = "templates/$template.tpl.php";
    } else {
      $this->template = $template;
    }
  }
  
  public function assign($variable, $value)
  {
    $this->variables[$variable] = $value;
  }
  
  public function goToPage($url, $message = '', $level='info') {
    if ( $message ) {
      $_SESSION['__message_text'] = $message;
      $_SESSION['__message_level'] = $level;
    }
    header("location: $url");
    die();
  }
  
  public function getScriptName(){
    return basename($_SERVER['SCRIPT_FILENAME'], '.php');
  }
  
  public function setOnLoad($code=''){
    $this->on_load=$code;
  }

  /**
   * Sets a pattern specific variable, variables set by this function aren't
   * mandatory, and are only to provide customization to the default template
   *
   * @param string $variable the variable to be set
   * @param string $value the content that will override the default value
   * @return void
   */
  public function setPatternVariable($variable, $value)  {
    $this->pattern_variables[$variable] = $value;
  }
  
  /**
   * Adds a header to be sent just before display();
   *
   * @param string $header
   * @param string $value
   */
  public function addHeader($header, $value){
    $this->headers[$header]=$value;
  }
  
  public function setMainMenu($template, $fullpath = false)
  {
    if ( !$fullpath) {
      $this->main_menu_template = "templates/$template.tpl.php";
    } else {
      $this->main_menu_template = $template;
    }
  }
    
  public function setSecondaryMenu($template, $fullpath = false)
  {
    if ( !$fullpath) {
      $this->secondary_menu_template = "templates/$template.tpl.php";
    } else {
      $this->secondary_menu_template = $template;
    }
  }
}
