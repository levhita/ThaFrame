<?php
/**
 * Holds {@link Page} class
 * @package ThaFrame
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 * Provide basic template system
 * @package ThaFrame
 */
class TemplatePattern
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
   * Variables that belongs only to this pattern, used to customize the text and
   * appareance of the page
   * @var array
   */
  protected $pattern_variables = array();
  
  public function __construct($template='')
  {
    if ( empty($template) ) {
      $template = $this->getScriptName();
    }

    $this->setTemplate($template);
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
  
  public function getScriptName(){
    return basename($_SERVER['SCRIPT_FILENAME'], '.php');
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
   * Shows the given template
   *
   * Converts the $variables array into $Data object and sets any message that may
   * be in the $_SESSION and finally calls the given template
   * @return void
   */
  public function getAsString()
  {
    if ( !file_exists($this->template) ) {
      throw new InvalidArgumentException("'$this->template' doesn't exists");
    }
    
    $this->assign('PatternVariables', (object)$this->pattern_variables);
    $this->assign('_javascripts', $this->javascripts);
    
    /**
     * Convert all variables into an object 
     * @todo Backwards Compatibility Remove Before Release
     * **/
    $Data = (object)$this->variables;
    
    /** This object actually helps to do a variety of things inside templates
     * @var Helper
     * @todo remove in favor of Helper::getInstance();
     */
    $Helper = new HelperPattern(get_defined_vars());
    
    ob_start();
      include $this->template;
    return ob_get_clean();
  }
}