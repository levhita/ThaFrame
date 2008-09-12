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
  	  die();
  	}
  }
  
  function formatAsDate($date, $mode='long')
  {
    if ($mode == "long") {
      return date('F j, Y, g:i a', strtotime($date));
    }
    if ($mode == "medium") {
      return date('d/m/y H:m:s', strtotime($date));
    }
    if ($mode == "short") {
      return date('d/m', strtotime($date));
    }
  }
  
  function formatAsLongDate($date) {
    $new_format = date('j-n-Y', strtotime($date));
    $data       = split('-', $new_format);
    $meses = array(
        '1' => 'Enero',
        '2' => 'Febrero',
        '3' => 'Marzo',
        '4' => 'Abril',
        '5' => 'Mayo',
        '6' => 'Junio',
        '7' => 'Julio',
        '8' => 'Agosto',
        '9' => 'Septiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre'
      );
    return "{$data['0']} de " . $meses[$data['1']] . " del {$data['2']}";
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
  
  function formatAsMoney($number)
  {
    return '$ '.number_format($number, 2, '.', ', ');
  }
  function formatCondition($string)
  {
    $values = array('C'=>'Bueno', 'C1'=>'Regular', 'C2'=>'Malo', 'Pts'=>'Partes', 'CH'=>'Chatarra');
    return $string;//$values[$string];
  }
  
/*!
  @function num2letras ()
  @abstract Dado un n?mero lo devuelve escrito.
  @param $num number - N?mero a convertir.
  @param $fem bool - Forma femenina (true) o no (false).
  @param $dec bool - Con decimales (true) o no (false).
  @result string - Devuelve el n?mero escrito en letra.

*/
function formatAsText($num, $fem = false, $dec = false) {
//if (strlen($num) > 14) die("El n?mero introducido es demasiado grande");
   $matuni[2]  = "dos";
   $matuni[3]  = "tres";
   $matuni[4]  = "cuatro";
   $matuni[5]  = "cinco";
   $matuni[6]  = "seis";
   $matuni[7]  = "siete";
   $matuni[8]  = "ocho";
   $matuni[9]  = "nueve";
   $matuni[10] = "diez";
   $matuni[11] = "once";
   $matuni[12] = "doce";
   $matuni[13] = "trece";
   $matuni[14] = "catorce";
   $matuni[15] = "quince";
   $matuni[16] = "dieciseis";
   $matuni[17] = "diecisiete";
   $matuni[18] = "dieciocho";
   $matuni[19] = "diecinueve";
   $matuni[20] = "veinte";
   $matunisub[2] = "dos";
   $matunisub[3] = "tres";
   $matunisub[4] = "cuatro";
   $matunisub[5] = "quin";
   $matunisub[6] = "seis";
   $matunisub[7] = "sete";
   $matunisub[8] = "ocho";
   $matunisub[9] = "nove";

   $matdec[2] = "veint";
   $matdec[3] = "treinta";
   $matdec[4] = "cuarenta";
   $matdec[5] = "cincuenta";
   $matdec[6] = "sesenta";
   $matdec[7] = "setenta";
   $matdec[8] = "ochenta";
   $matdec[9] = "noventa";
   $matsub[3]  = 'mill';
   $matsub[5]  = 'bill';
   $matsub[7]  = 'mill';
   $matsub[9]  = 'trill';
   $matsub[11] = 'mill';
   $matsub[13] = 'bill';
   $matsub[15] = 'mill';
   $matmil[4]  = 'millones';
   $matmil[6]  = 'billones';
   $matmil[7]  = 'de billones';
   $matmil[8]  = 'millones de billones';
   $matmil[10] = 'trillones';
   $matmil[11] = 'de trillones';
   $matmil[12] = 'millones de trillones';
   $matmil[13] = 'de trillones';
   $matmil[14] = 'billones de trillones';
   $matmil[15] = 'de billones de trillones';
   $matmil[16] = 'millones de billones de trillones';

   $num = trim((string)@$num);
   if ($num[0] == '-') {
      $neg = 'menos ';
      $num = substr($num, 1);
   }else
      $neg = '';
   while ($num[0] == '0') $num = substr($num, 1);
   if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
   $zeros = true;
   $punt = false;
   $ent = '';
   $fra = '';
   for ($c = 0; $c < strlen($num); $c++) {
      $n = $num[$c];
      if (! (strpos(".,'''", $n) === false)) {
         if ($punt) break;
         else{
            $punt = true;
            continue;
         }

      }elseif (! (strpos('0123456789', $n) === false)) {
         if ($punt) {
            if ($n != '0') $zeros = false;
            $fra .= $n;
         }else

            $ent .= $n;
      }else

         break;

   }
   $ent = '     ' . $ent;
   if ($dec and $fra and ! $zeros) {
      $fin = ' coma';
      for ($n = 0; $n < strlen($fra); $n++) {
         if (($s = $fra[$n]) == '0')
            $fin .= ' cero';
         elseif ($s == '1')
            $fin .= $fem ? ' una' : ' un';
         else
            $fin .= ' ' . $matuni[$s];
      }
   }else
      $fin = '';
   if ((int)$ent === 0) return 'Cero ' . $fin;
   $tex = '';
   $sub = 0;
   $mils = 0;
   $neutro = false;
   while ( ($num = substr($ent, -3)) != '   ') {
      $ent = substr($ent, 0, -3);
      if (++$sub < 3 and $fem) {
         $matuni[1] = 'una';
         $subcent = 'as';
      }else{
         $matuni[1] = $neutro ? 'un' : 'uno';
         $subcent = 'os';
      }
      $t = '';
      $n2 = substr($num, 1);
      if ($n2 == '00') {
      }elseif ($n2 < 21)
         $t = ' ' . $matuni[(int)$n2];
      elseif ($n2 < 30) {
         $n3 = $num[2];
         if ($n3 != 0) $t = 'i' . $matuni[$n3];
         $n2 = $num[1];
         $t = ' ' . $matdec[$n2] . $t;
      }else{
         $n3 = $num[2];
         if ($n3 != 0) $t = ' y ' . $matuni[$n3];
         $n2 = $num[1];
         $t = ' ' . $matdec[$n2] . $t;
      }
      $n = $num[0];
      if ($n == 1) {
         $t = ' ciento' . $t;
      }elseif ($n == 5){
         $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
      }elseif ($n != 0){
         $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
      }
      if ($sub == 1) {
      }elseif (! isset($matsub[$sub])) {
         if ($num == 1) {
            $t = ' mil';
         }elseif ($num > 1){
            $t .= ' mil';
         }
      }elseif ($num == 1) {
         $t .= ' ' . $matsub[$sub] . '?n';
      }elseif ($num > 1){
         $t .= ' ' . $matsub[$sub] . 'ones';
      }
      if ($num == '000') $mils ++;
      elseif ($mils != 0) {
         if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
         $mils = 0;
      }
      $neutro = true;
      $tex = $t . $tex;
   }
   $tex = $neg . substr($tex, 1) . $fin;
   $tex = ucfirst($tex);
   if ($tex=='Ciento') {
     return "Cien";
   }
   return $tex;
}

function t(){
  global $_translation;
  if ( func_num_args()<= 0 ) {
    throw new LengthException('Translation function expects at least one parameter.');
  }
  
  $original = func_get_arg(0);
  $search = array();
  $replace = array();
  
  for ($i = 1;$i < func_num_args();$i++) {
    $search[]   = "%$i%";
    $replace[]  = func_get_arg($i);
  }
  
  if ( array_key_exists($original,$_translation) ){
    $original = $_translation[$original];
  }
  return str_replace($search, $replace, $original);
}
?>
