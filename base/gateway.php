<?php
/**
 * Provides a gateway between files that are menat to be served directly by
 * the webserver, without having to make thaframe publicy accesible.
 *
 * @package ThaFrame
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 19
 * @filesource
 */

define('TO_ROOT', '.');
include TO_ROOT . "/includes/main.inc.php";

$file     = $_GET['file'];

/** Sanitize access to folder up in the hierarchy **/
if(strpos($file,"../")!==FALSE){
  header("HTTP/1.0 403 Forbidden");
  loadErrorPage('403');
}

$filename = THAFRAME . "/gateway/$file";

if ( !file_exists($filename) ) {
  header("HTTP/1.0 404 Not Found");
  loadErrorPage('404');
}

$mimetype = mime_content_type($filename);
header("Content-type: $mimetype");

if ( @readfile($filename)===false ) {
  header("HTTP/1.0 403 Forbidden");
  loadErrorPage('403');
}