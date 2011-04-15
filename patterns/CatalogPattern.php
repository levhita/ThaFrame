<?php
/**
 * Holds {@link CatalogPattern} class
 * @author Argel Arias <levhita@gmail.com>
 * @package ThaFrame
 * @copyright Copyright (c) 2007, Argel Arias <levhita@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */


/**
 * Provides a {@link PagePattern} that shows an item's list.
 *
 * Trough several methods it allows you to add links and actions asociated
 * with each row, as well as formating
 * @package ThaFrame
 */
class CatalogPattern extends PagePattern
{
   /**
   * Construct a {@link CatalogPattern} page
   * @param string $page_name the page name to be shown
   * @param string $template by default it uses CatalogPattern.{$view}.tpl.php 
   * @return CatalogPattern
   */
  public function __construct($page_name='', $template='')
  {
    $view = ( array_search($GET['_view'] , array('list','detail','edit') ) )?$view:'list';
  	
    if ( empty($template) ) {
      $this->setTemplate(THAFRAME . "/patterns/templates/CatalogPattern.$view.tpl.php", true);
    } else {
      $this->setTemplate( $template);
    }
    
    $this->setPageName($page_name);
  }
  
  /**
   * Display the selected template with the given data and customization
   * @return void
   /
  public function display() {
    parent::display();
  }*/
}