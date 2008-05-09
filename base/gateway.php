<?php
define('TO_ROOT', '.');
include TO_ROOT . "/includes/main.inc.php";

$file     = $_GET['file'];
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