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
    if ( isset($_SESSION['id_usuario']) ) {
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
  
  
  function createComboBox($items, $name, $selected, $extra_parameters='')
  {
    $output= "<select name=\"$name\" id=\"$name\" $extra_parameters>\n";
    foreach ( $items as $key => $value )
    {
      $output.= "<option value=\"$key\"";
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
      $output.= "<input type=\"radio\" name=\"$name\" value=\"$key\"";
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
    list($year, $month, $day) = split('[-]', $date);
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
    return mysql_escape_string($value);
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
?>
