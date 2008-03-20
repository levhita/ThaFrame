<?php
  /**
   * Holds general use functions
   * @package ThaFrame
   * @author Argel Arias <levhita@gmail.com>
   * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
   * @license http://opensource.org/licenses/gpl-license.php GNU Public License
   */
  
  
	/**
 	 * Asserts that you are logged in
	 * @todo change to configured message and page.
	 */	
	function assertLoggedIn($Page) {
    if ( isset($_SESSION['id_usuario']) ) {
      if ( $_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] ) {
        session_destroy();
        session_start();
        $Page->goToPage(TO_ROOT .'/index.php', "Ggrr.. tu ip no coincide con la cual iniciaste sesión!");
      }
    } else {
      $Page->goToPage(TO_ROOT .'/index.php', "Tu sesión ha vencido, por favor vuelve a iniciar");
    }
  }
  
  function assertLoggedInAjax() {
    
    if ( isset($_SESSION['id_usuario']) ) {
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
  
  function cleanToDb($value, $DbConnection=null)
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
      $output.=">$value</option>\n";
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
      $output.=" $extra_parameters/>$value ";
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
?>
