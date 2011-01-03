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
    $file_name = "{$class_name}.inc.php";
    
    $paths = array (
      THAFRAME . "/includes",  
      THAFRAME . "/patterns",
      THAFRAME . "/models",
      TO_ROOT . "/includes",  
      TO_ROOT . "/models",
    );
    
    foreach($paths AS $path) {
      $file="$path/$file_name";
      if ( file_exists($file) ) {
        include_once $file;
        return true;
      }
    }
    return false;
  }
  
  /**
   * Configure autoloading using Core
   *
   * This is designed to play nicely with other autoloaders.
   */
  public static function registerAutoload()
  {
    spl_autoload_register(array('Autoloader', 'autoload'));
  }
}