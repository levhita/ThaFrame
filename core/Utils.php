<?php

class Utils {
  
  /**
   * Holds if the page is being visited by a mobile browser
   * @var boolean
   */
  private static $__mobile = null;
  
  private function __construct(){}
  
  public static function isMobile() {
    if(!isset(self::$__mobile) ) {
      $useragent=$_SERVER['HTTP_USER_AGENT'];
      if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent,0,4))) {
        return self::$__mobile = true;
      } else {
        return self::$__mobile = false;
      }
    }
    return self::$__mobile;
  }
  
  public static function validateNotEmpty($field)
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
  
  public static function cleanToDbBinary($value, $DbConnection=null)
  {
    if ( is_null($DbConnection) ) {
      global $DbConnection;
    }
    return mysql_real_escape_string($value, $DbConnection->getMysqlConnection());
  }
  
  public static function cleanToDb($value)
  {
    return mysql_real_escape_string($value);
  }
  
  public static function selfURL()
  {
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
  }
  
  public static function microtime_float()
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
  public static function cleanDateFromData(&$data, $field){
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
  public static function preg_ls ($path=".", $recursive=false, $pattern="/.*/") {
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
  
  public static function getOptions($table_name, DbConnection $DbConnection=null)
  {
    if (is_null($DbConnection) ) {
      $DbConnection = DbConnection::getInstance();
    }
    
    $sql = "SELECT id_$table_name, nombre
            FROM $table_name";
    return $DbConnection->getArrayPair($sql);
  }
  
}