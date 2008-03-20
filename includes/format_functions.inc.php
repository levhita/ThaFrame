<?php
  /**
   * Holds formating functions to use in templates
   * @package ThaFrame
   * @author Argel Arias <levhita@gmail.com>
   * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
   * @license http://opensource.org/licenses/gpl-license.php GNU Public License
   */
  
  /**
   * Returns the string 'Si' or 'No' depending if $text is equal to '1' or not
   * @param  string $text the text to be matched
   * @return string
   */
  function siNo($text)
  {
    if ( $text=='1' ) {
      return 'Sí';
    }
    return 'No';
  }
  
  /**
   * Returns the given number in money format
   * @param string $number The number
   * @return string
   */
  function money($number)
  {
    return '$ ' . number_format($number, 2, '.', ',');
  }
  
  /**
   * Returns the given date in a friendly manner
   * @param string $date The date in any format supported by {@link strtotime()}
   * @param string $format They are 3 formats supported: 'long', 'medium' and 'short'
   * @filesource
   * @return string
   */
  function formatAsDate($date, $format='long')
  {
    if ($format == "long") {
      return date('F j, Y, g:i a', strtotime($date));
    }
    if ($format == "medium") {
      return date('d/m/y H:m:s', strtotime($date));
    }
    if ($format == "short") {
      return date('d/m', strtotime($date));
    }
  }
?>
