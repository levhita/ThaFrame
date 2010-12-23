<?php
  /**
   * Holds general use functions
   * @package ThaFrame
   * @author Argel Arias <levhita@gmail.com>
   * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
   * @license http://opensource.org/licenses/gpl-license.php GNU Public License
   */

require_once(THAFRAME."/includes/format_functions.inc.php");
  
	/**
 	 * Asserts that you are logged in
	 * @todo change to configured message and page.
	 */
	function assertLoggedIn($Page) {
    if ( isset($_SESSION['user_id']) ) {
      if ( $_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] ) {
        session_destroy();
        session_start();
        $Page->goToPage(TO_ROOT .'/index.php', "Ggrr.. tu ip no coincide con la cual iniciaste sesión!", 'warning');
      }
    } else {
      $Page->goToPage(TO_ROOT .'/index.php', "Tu sesión ha vencido, por favor vuelve a iniciar", 'warning');
    }
  }
  
  function assertLoggedInAjax() {
    
    if ( isset($_SESSION['user_id']) ) {
      if ( $_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] ) {
        return false;
      }
    } else {
      return false;
    }
    return true;
  }
  
  function validateNotEmpty($field)
  {
    if ( isset($_POST[$field]) ) {
      $field_value = trim($_POST[$field]);
      if ( !empty($field_value) ) {
        return true;
      }
      return false;
    }
    return false;
  }
  
  function cleanToDbBinary($value, $DbConnection=null)
  {
    if ( is_null($DbConnection) ) {
      global $DbConnection;
    }
    return mysql_real_escape_string($value, $DbConnection->getMysqlConnection());
  }
  
  function getOptions($table_name, $DbConnection)
  {
    $sql = "SELECT id_$table_name, nombre
            FROM $table_name";
    return $DbConnection->getArrayPair($sql);
  }
  
  
  /**
   * Creates a ComboBox
   * @param $items Array in $key=>$value format
   * @param $name String field name
   * @param $selected string telling which value is the one selected
   * @param $extra_parameters string any other extra string to be attached at the end
   * @return string the combobox in a single string
   */
  function createComboBox($items, $name, $selected, $extra_parameters='')
  {
    $output= "<select name=\"$name\" id=\"$name\" $extra_parameters>\n";
    foreach ( $items as $key => $value )
    {
      $output.= "<option value=\"" . htmlentities($key) . "\"";
      if ( $key==$selected ) {
        $output .=" selected=\"selected\" ";
      }
      $output.=">".t(ucwords($value))."</option>\n";
    }
    $output.="</select>\n";
    return $output;
  }
  
  function createRadioButton($items, $name, $selected, $extra_parameters='')
  {
    foreach ( $items as $key => $value )
    {
      $output.= "<input type=\"radio\" name=\"$name\" value=\"" . htmlentities($key) . "\"";
      if ( $key==$selected ) {
        $output .=" checked=\"checked\" ";
      }
      $output.=" $extra_parameters/>".t(ucwords($value))." ";
    }
    return $output;
  }
  
  function createDateComboBox( $date='', $past = 10, $future = 10, $prefix='date')
  {
    if ( $date=='' ) {
      $date=date('Y-m-d');
    }
    list($year, $month, $day) = explode('-', $date);
    $current_year = date('Y');

    $years = array();
    for($x = $current_year-$past; $x <= $current_year+$future; $x++)
    {
      $years[$x] = $x;
    }
    $year_combo=createComboBox($years, $prefix.'_year', $year);
    
    $months = array (
      '01'=>'Enero',
      '02'=>'Febrero',
      '03'=>'Marzo',
      '04'=>'Abril',
      '05'=>'Mayo',
      '06'=>'Junio',
      '07'=>'Julio',
      '08'=>'Agosto',
      '09'=>'Septiembre',
      '10'=>'Octubre',
      '11'=>'Noviembre',
      '12'=>'Diciembre',
      );
    $month_combo=createComboBox($months, $prefix.'_month', $month);
    
    $days = array();
    for($x = 1; $x <= 31; $x++)
    {
      $days[$x] = $x;
    }
    $day_combo=createComboBox($days, $prefix.'_day', $day);
    return  $day_combo . $month_combo . $year_combo ;
  }
  /**
   * loads an Error Page.
   *
   * @param string $page
   * @todo make it posible to load a php file with the original url as parameter.
   */
  function loadErrorPage($page){
    if(file_exists(TO_ROOT ."/pages/$page.html")){
      header("Location: ". SYSTEM_WEB_ROOT . "/pages/$page.html");
    } else {
      header('Content-Type: text/html; charset=UTF-8');
      readfile(THAFRAME . "/pages/$page.html");
    }
    die();
  }
  
  function cleanToDb($value)
  {
    return mysql_real_escape_string($value);
  }
  
  function selfURL()
  {
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
  }
  
  function strleft($s1, $s2)
  {
    return substr($s1, 0, strpos($s1, $s2));
  }
  
  function t(){
    global $_translation;
    if ( func_num_args()<= 0 ) {
      throw new LengthException('Translation function expects at least one parameter.');
    }
    
    $original = (string)func_get_arg(0);
    $search = array();
    $replace = array();
    
    for ($i = 1;$i < func_num_args();$i++) {
      $search[]   = "%$i%";
      $replace[]  = func_get_arg($i);
    }
    if ( array_key_exists($original, $_translation) ){
      $original = $_translation[$original];
    }
    return str_replace($search, $replace, $original);
  }


  
  function microtime_float()
  {
    list($useg, $seg) = explode(" ", microtime());
    return ((float)$useg + (float)$seg);
  }
  
  /**
   * Takes the $data from a form with a date on it, creates a date by compositing
   * the year,month and day fields, into one string, and then cleans the extra
   * fields
   * @param $data Array An Array of fields
   * @param $field String Field name to be cleaned
   * @return bool True on success FALSE in case of an error
   */
  function cleanDateFromData(&$data, $field){
    $date = $data["{$field}_year"] . "-" . $data["{$field}_month"] . "-" .$data["{$field}_day"];
    unset($data["{$field}_year"], $data["{$field}_month"], $data["{$field}_day"]);
    $data[$field] = $date;
    return TRUE;
  }
  
  /**
   * An ls style command using regular expresions
   * 
   * By fordiman@gmail.com taken from PHP documentation:
   * http://www.php.net/manual/en/class.dir.php#60562
   *
   * @example foreach (preg_ls("/etc/X11", true, "/.*\.conf/i") as $file) echo $file."\n";
   * @param string $path
   * @param boolean $recursive if the ls should be recursive
   * @param string $patttern the pattern in regular expression format
   * @return Array
   */ 
  function preg_ls ($path=".", $recursive=false, $pattern="/.*/") {
    $rec = $recursive;
    $pat = $pattern;
    // it's going to be used repeatedly, ensure we compile it for speed.
    $pat=preg_replace("|(/.*/[^S]*)|s", "\\1S", $pat);
    //Remove trailing slashes from path
    while (substr($path,-1,1)=="/") $path=substr($path,0,-1);
    //also, make sure that $path is a directory and repair any screwups
    if (!is_dir($path)) $path=dirname($path);
    //assert either truth or falsehoold of $rec, allow no scalars to mean truth
    if ($rec!==true) $rec=false;
    //get a directory handle
    $d=dir($path);
    //initialise the output array
    $ret=Array();
    //loop, reading until there's no more to read
    while (false!==($e=$d->read())) {
        //Ignore parent- and self-links
        if (($e==".")||($e=="..")) continue;
        //If we're working recursively and it's a directory, grab and merge
        if ($rec && is_dir($path."/".$e)) {
            $ret=array_merge($ret,preg_ls($path."/".$e,$rec,$pat));
            continue;
        }
        //If it don't match, exclude it
        if (!preg_match($pat,$e)) continue;
        //In all other cases, add it to the output array
        $ret[]=$path."/".$e;
    }
    //finally, return the array
    return $ret;
}
?>
