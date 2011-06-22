<?php
/**
 * Helper class that holds methods that help in the templates
 * @package ThaFrame
 * @author Argel Arias <levhita@gmail.com>
 * @copyright Copyright (c) 2008, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

class HelperPattern {
  
  /*
   * Data from the Page;
   * @var object
   */
  protected $Data;
  
  public function __construct(&$Data)
  {
    $this->Data = $Data;
  }
  
  /*
   * Load a subtemplate, chosing between a custom template or a standard
   * framework subtemplate
   */
  public function loadSubTemplate($template, $fullpath=FALSE, $variables='')
  {
    $vars=array();
    if($variables!='') {
      $raw_vars = explode(',', $variables);
      foreach($raw_vars AS $raw_var) {
        list($field, $value) = explode(':', $raw_var);
        $vars["__sub_$field"] = $value;
      } 
    }
    $Data   = $this->Data;
    $Helper = $this;
    if (!$fullpath) {
      if ( file_exists(TO_ROOT . "/subtemplates/$template.tpl.php") ) {
        $data=(array)$Data;unset($data['template']);extract($data);extract($vars);
        include TO_ROOT . "/subtemplates/$template.tpl.php";
      } else {
        if ( file_exists( THAFRAME . "/subtemplates/$template.tpl.php") ) {
          $data=(array)$Data;unset($data['template']);extract($data);extract($vars);
          include THAFRAME . "/subtemplates/$template.tpl.php";
        } else {
          throw new Exception("Couldn't find template '$template'");
        }
      }
    } else {
      if ( file_exists( $template) ) {
        $data=(array)$Data;unset($data['template']);extract($data);extract($vars);
        include $template;
      } else {
        throw new Exception("Couldn't find template '$template'");
      }
    }
  }
  
  public static function createFrameLink($filename, $return_string=FALSE, $absolute_path=FALSE)
  {
    $Config = Config::getInstance();
    $string = '';
    if ($absolute_path){
      
      if( file_exists(TO_ROOT ." /$filename") ) {
        $string = $Config->system_web_root . "/$filename";
      } else {
        $string = $Config->system_web_root . "/f/$filename";
      }  
    } else {
      if( file_exists(TO_ROOT . "/$filename") ){
        $string = TO_WEB_ROOT. "/$filename";
      } else {
        $string = TO_WEB_ROOT . "/f/$filename";
      }
    }
    
    if ($return_string){
      return $string;
    }
    echo $string;
  }
  
  public static function createSelfUrl($parameters, $return_string=FALSE) {
    global $web_root;
    $string .= $web_root. $_SERVER['PHP_SELF'];
    $original_parameters = $_GET;
    $parameters = array_merge($original_parameters, $parameters);
    if ( count($parameters) ) {
      $first=TRUE;
      foreach($parameters as $variable=>$value) {
        $string .=($first)?'?':'&';
        $string .= urlencode($variable) . "=" . urlencode($value);
        $first = FALSE;
      }
    }
    if ($return_string) {
      return $string;
    }
    echo $string;
  }
  
  public static function createActionCall($action, $title, $field='', $value='', $icon='',
  $is_ajax=FALSE , $return_string=FALSE, $absolute_path = FALSE)
  {
    $string = "";
    $title = t($title);
    if( $is_ajax ) {
      $string .= "<a href=\"javascript:void(xajax_$action($value))\" title=\"$title\">";
    } else {
      if ( !empty($field) ) {
        $string .= "<a href=\"$action?$field=$value\" title=\"$title\">";
      } else {
        $string .= "<a href=\"$action\" title=\"$title\">";
      }
    }
    if ( !empty($icon) ) {
      $icon =  self::createFrameLink($icon, TRUE, $absolute_path);
      $string .= "<img src=\"$icon\" alt=\"$title\"/>";
    }
    $string .= "$title</a>";
    
    if ($return_string){
      return $string;
    }
    echo $string;
  }
  
  public static function objetizeArray($array){
    $clean_array = array();
    foreach($array AS $key => $value){
      $clean_array[$key] = htmlspecialchars($value);
    }
    return (object)$clean_array;
  }
  
  /**
   * Creates a ComboBox
   * @param $items Array in $key=>$value format
   * @param $name String field name
   * @param $selected string telling which value is the one selected
   * @param $extra_parameters string any other extra string to be attached at the end
   * @return string the combobox in a single string
   */
  public static function createComboBox($items, $name, $selected, $extra_parameters='')
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
  
  public static function createRadioButton($items, $name, $selected, $extra_parameters='')
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
  
  public static function createDateComboBox( $date='', $past = 10, $future = 10, $prefix='date')
  {
    if ( $date=='' ) {
      $date=date('Y-m-d');
    }
    list($year, $month, $day) = explode('-', $date);
    $current_year = date('Y');
	
    /*
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
    return  $day_combo . $month_combo . $year_combo ;*/
    return "<input type='date' class='date' name='$prefix' value='$date'>";
  }
  
  public static function getMenus($menu, $selected='') {
    $filename = TO_ROOT."/configs/menus/$menu.ini";
    if ( !file_exists($filename) ) {
      throw new InvalidArgumentException("Filename '$filename' doesn\'t exist.");
    }
    if( !$menus = @parse_ini_file($filename, true ) ) {
      throw new InvalidArgumentException("Filename '$filename' has errors.");
    }
    if(file_exists('menu.ini')) {
      if( !$local = @parse_ini_file('menu.ini', true) ) {
        throw new InvalidArgumentException("Filename 'menu.ini' has errors.");
      }
      $menus = array_merge($menus, $local); 
    }
    return $menus;
  }
}
