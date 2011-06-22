<?php
/**
 * Holds {@link Page} class
 * @package ThaFrame
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

define('GOTO_MESSAGE_INFO', 'info');
define('GOTO_MESSAGE_WARNING', 'warning');
define('GOTO_MESSAGE_ERROR', 'error');
define('GOTO_MESSAGE_SUCCESS', 'success');

/**
 * Provide basic template system and http some http functionality
 * @package ThaFrame
 */
class PagePattern
{
  /**
   * Holds the variables to be passed to the template as $Data object
   * @var array
   */
  protected $_variables = array();

  /**
   * Holds the javascripts that should be added at the head section
   * @var array
   */
  protected $_javascripts = array();

  /**
   * Holds the relative path to the template
   * @var string
   */
  protected $_template = '';

  /**
   * Holds the relative path to the layout
   * @var string
   */
  protected $_layout = '';

  /**
   * Script to run on page load
   *
   * @var string
   */
  public $_on_load   = '';

  /**
   * Holds the header to be sent on_display
   *
   * @var array
   */
  public $_headers = array();
  
  /**
   * Holds the html headers to be included inside <head>
   *
   * @var array
   */
  public $_html_headers = array();

  /**
   * Variables that belongs only to this pattern, used to customize the text and
   * appareance of the page
   * @var array
   */
  protected $_pattern_variables = array();
  /**
   * Holds the main menu template
   * @var string
   */
  protected $_main_menu_template = '';
  /**
   * Holds the secondary menu template
   * @var string
   */
  protected $_secondary_menu_template = '';


  public function __construct($page_name='', $template='', $layout='')
  {
    if( empty($template) ){
      $template = $this->getScriptName();
    }

    $this->setPageName($page_name);
    $this->setTemplate($template);
    $this->setLayout($layout);
  }



  /**
   * Jumps to the given url, sending a message.
   * @param string $url
   * @param string $message
   * @param string $level might be: info, warning, error, success
   * @return false
   */
  public static function goToPage($url, $message = '', $level='info') {
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


  public function assign($variable, $value)
  {
    $this->_variables[$variable] = $value;
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
    if (!$fullpath) {
      $this->_template = "templates/$template.tpl.php";
    } else {
      $this->_template = $template;
    }
  }

  /**
   * Sets the layout file to be used.
   *
   * Take for granted that the file is under the relative path "templates/" and
   * has a "tpl.php" extension, unless you set $fullpath to true
   * @param string  $layout The name of the layout to be used
   * @param bool    $fullpath overrides the naming convention and allows you to set any file
   * @return void
   */
  public function setLayout($layout, $fullpath = false)
  {
    if ( !empty($layout) && !$fullpath){
      $this->_layout = TO_ROOT . "/subtemplates/{$layout}_layout.tpl.php";
    } else {
      $this->_layout = $layout;
    }
  }

  public function setMainMenu($template, $fullpath = false)
  {
    if ( !empty($template) && !$fullpath) {
      $this->_main_menu_template = TO_ROOT . "/subtemplates/{$template}_main_menu.tpl.php";
    } else {
      $this->_main_menu_template = $template;
    }
  }

  public function setSecondaryMenu($template, $fullpath = false)
  {
    if ( !empty($template) && !$fullpath) {
      $this->_secondary_menu_template = "templates/$template.tpl.php";
    } else {
      $this->_secondary_menu_template = $template;
    }
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
    $this->_pattern_variables[$variable] = $value;
  }

  /**
   * Sets the page name depending of the layout it might be translated
   * @param string $page_name
   * @return void
   */
  public function setPageName($page_name) {
    $this->assign('__page_name', $page_name);
  }

  /** Sets the javascript code to be run when the pase finishes loading
   *
   * @param string $code javascript code
   * @return void
   */
  public function setOnLoad($code=''){
    $this->_on_load=$code;
  }

  /**
   * Adds a header to be sent just before display();
   *
   * @param string $header
   * @param string $value
   */
  public function addHeader($header, $value){
    $this->_headers[$header]=$value;
  }

  /**
   * Adds a javascript that will be added in the head section
   * @param string $javascript the javascript code
   * @return void
   */
  public function addJavascript($javascript)
  {
    $this->_javascripts[] = $javascript;
  }
  
  /**
   * Adds a html header that will be added in the <head> section
   * @param string $html_header the header code
   * @return void
   */
  public function addHTMLHeader($html_header)
  {
    $this->_html_headers[] = $html_header;
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

    /** Main Menu Cascade Loader **/
    $script_name = $this->getScriptName();
    if ( empty($this->_main_menu_template) ) {
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

    /** Secondary Menu Cascade Loader**/
    if ( empty($this->_secondary_menu_template) ) {
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

    /** Layout Cascade Loader **/
    if ( empty($this->_layout) ) {
      if ( file_exists("templates/" . $this->getScriptName() . "_layout.tpl.php") ) {
        $this->setLayout($this->getScriptName()."_menu");
      } else if (file_exists('templates/default_layout.tpl.php') ) {
        $this->setLayout('default_layout');
      } else if (file_exists(TO_ROOT. "/subtemplates/default_layout.tpl.php") ) {
        $this->setLayout(TO_ROOT. '/subtemplates/default_layout.tpl.php', TRUE);
      } else {
        $this->setLayout(THAFRAME. '/subtemplates/default_layout.tpl.php', TRUE);
      }
    }

    $this->assign('PatternVariables', (object)$this->_pattern_variables);
    $this->assign('__javascripts', $this->_javascripts);
    $this->assign('__html_headers', $this->_html_headers);
    $this->assign('__main_menu_template', $this->_main_menu_template);
    $this->assign('__secondary_menu_template', $this->_secondary_menu_template);
    $this->assign('__on_load', $this->_on_load);
    
    $Data = (object)$this->_variables; // @todo: Backwards compatibility remove before release
    extract($this->_variables);
    
    /**
     * This object helps to do a variety of things inside templates
     * @var Helper
     */
    $Helper = new HelperPattern($Data);

    $_content_ = self::runTemplate($this->_template, $this->_variables);

    /** Send the headers **/
    foreach($this->_headers AS $header => $value)
    {
      header("$header: $value");
    }

    /** Include it in the Layout **/
    include $this->_layout;
  }

  /**
   * Run the Template in the cleanest enviroment posible
   * @param unknown_type $template
   * @param unknown_type $data
   * @return unknown_type
   */
  private static function runTemplate($_template_, $_data_) {
    $Data = (object)$_data_; /// @todo Backwards Compatibility Remove Before Release
    extract($_data_);
    $Helper = new HelperPattern(get_defined_vars());

    ob_start();
      include $_template_;
      $_content_ = ob_get_contents();
    ob_end_clean();

    return $_content_;
  }
}