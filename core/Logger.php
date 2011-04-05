<?php
/**
 * A simple Logger, to be used by programmers doing some debug.
 *
 * @todo Add report level(kind of php basic report levels, an oportunity to use
 * bit operators!!!).
 * @todo Add File and Line handling
 *
 * @package Garson
 * @author Argel Arias <levhita@gmail.com>
 */
class Logger {

  /**
   * Holds the logs of the session
   * @var array
   */
  protected static $logs = array();
  protected function __construct(){}
  
  /**
   * Logs details
   * @todo validate that level has a valid value
   * @param string $name
   * @param string $details
   * @param string $level Valid values are: notice, warning, error
   * @return null
   */
  public static function log($name, $details='', $level='notice'){
    $Config = Config::getInstance();
    
    /* Creates Log array */
    $log = array (
            'time'    => date("D M j G:i:s T Y"),
            'level'   => $level,
            'name'    => $name,
            'uri'  => $_SERVER['REQUEST_URI'],
            'details' => $details,
      );
    /* Save it in the instance */
    self::$logs[] = $log;
    
    if ($Config->log_level == 'both' || $Config->log_level == 'text' ) {
      $file_pointer = fopen($Config->log_file, 'a');
      fputcsv($file_pointer, $log);
    }
  }
  
  /**
   * Returns an array with the logs to be printed.
   * @return Array With all the logs, in the case that the log_level doesn't
   * allow to print the error, it returns false.
   */
  public static function getWebLog() {
    $Config = Config::getInstance();
    if ($Config->log_level == 'none' || $Config->log_level == 'text' ) {
      return false;
    }
    return self::$logs;
  }
  /**
   * Gets the log array
   * @return array
   */
  public static function getLog() {
    self::$logs;
  }
}