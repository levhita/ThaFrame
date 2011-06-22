<?php
/**
 * Holds {@link CalendarPattern} class
 * @author Argel Arias <levhita@gmail.com>
 * @package ThaFrame
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */


/**
 * Provides a {@link PagePattern} that shows an fullcalendar jquery plugin.
 *
 * @package ThaFrame
 */

class CalendarPattern extends PagePattern
{
   /**
   * Construct a {@link CalendarPattern} page
   * @param string $page_name the page name to be shown
   * @param string $template by default it uses CatalogPattern.{$view}.tpl.php 
   * @return CalendarPattern
   */
  public function __construct($page_name='', $template='')
  {
    if ( empty($template) ) {
      $this->setTemplate(THAFRAME . "/patterns/templates/CalendarPattern.tpl.php", true);
    } else {
      $this->setTemplate( $template);
    }
    $this->setPageName($page_name);
    $html_header = '<script type="text/javascript" src="' . HelperPattern::createFrameLink('vendors/jqueryui/js/fullcalendar/fullcalendar.min.js', true) . '"></script>';
    $this->addHTMLHeader($html_header);
  }
  
}