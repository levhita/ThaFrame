<?php
/**
 * Include files based on the Instance's ClassName being created
 *
 * Gets {@link Controller} from application/controllers/
 * Gets {@link View}s from application/views/
 * Gets {@link Model}s from application/models/
 * Every other class it gets it from application/core/
 *
 * For classes that doesn't form part of the core, you must include the file
 * by hand.
 *
 * @package Garson
 */
class Autoloader {
 
  public static function autoload($class_name)
  {
    $class_name = ucwords($class_name);
		
    /** Specific classes are inside this directories, either in app space, or in framework space. **/
    $named_directories = array
    (
		'Pattern' => 'patterns/',
		'Model' => 'models/',
    );

    $is_core = true;

    foreach ( $named_directories AS $name => $directory ) {
      if ( stristr( $class_name, $name ) && $class_name != $name ) {
        $path = $directory . $class_name;
        $is_core = false;
        break;
      }
    }
	
	/** All other classes are inside the core **/
    if ( $is_core ) {
      $path = 'core/' . $class_name;
    }
      
    /** Try to include it from the application first **/
    if ( file_exists(TO_ROOT . '/' . $path . '.php') ) {
      require_once TO_ROOT . '/' . $path . '.php';
      return true;
    }
    
    /** If that fails,  includes it from ThaFrame **/
    if ( file_exists(THAFRAME . '/' . $path . '.php') ) {
      require_once THAFRAME . '/' . $path . '.php';
      return true;
    }
    return false;
  }
  
  /**
   * Configure autoloading
   *
   * This is designed to play nice with other autoloaders.
   */
  public static function registerAutoload()
  {
    spl_autoload_register(array('Autoloader', 'autoload'));
  }
}