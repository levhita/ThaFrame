<?php
/**
 * Holds {@link Listing} class
 * @author Argel Arias <levhita@gmail.com>
 * @package ThaFrame
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once THAFRAME . "/patterns/Page.inc.php";

/**
 * Provides a {@link Page} that shows an item's list.
 *
 * Trough several methods it allows you to add links and actions asociated
 * with each row, as well as formating
 * @package Freimi
 * @todo paging , column re-ordering
 */
class Catalog extends Page
{
   /**
   * Construct a {@link Listing} page
   * @param string $page_name the page name to be shown
   * @param string $template by default it uses Listing.tpl.php 
   * @return Listing
   */
  public function __construct($page_name, $template='')
  {
    $view = ( array_search($GET['_view'] , array('list','detail','edit') ) )?$view:'list';
  	
    if ( empty($template) ) {
      $this->setTemplate(THAFRAME . "/patterns/templates/Catalog.$view.tpl.php", true);
    } else {
      $this->setTemplate( $template);
    }
    
    $this->assign('page_name', $page_name);
  }
  
  /**
   * Display the selected template with the given data and customization
   * @return void
   /
  public function display() {
    parent::display();
  }*/
}