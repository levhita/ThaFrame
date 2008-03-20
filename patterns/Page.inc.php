<?php
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
  
  public function __construct($page_name='', $template='')
  {
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
    if( isset($_SESSION['__message']) ) {
      $this->assign('__message', $_SESSION['__message']);
      unset($_SESSION['__message']);
    }
    
    $this->assign('javascripts', $this->javascripts);
    
    $Data = (object)$this->variables;
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
  
  public function goToPage($url, $message = '') {
    if ( $message ) {
      $_SESSION['__message'] = $message;
    }
    header("location: $url");
    die();
  }
}
?>
