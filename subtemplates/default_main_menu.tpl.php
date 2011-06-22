<?php
$_selected_menu_ = (empty($_selected_menu_))?'default':$_selected_menu_;
$_menus_ = HelperPattern::getMenus($_selected_menu_);
if ( isset($_menus_['_main_']['selected_tab']) ) {
  $_selected_tab_ = $_menus_['_main_']['selected_tab'];
  unset($_menus_['_main_']);
}
?>
<ul>
  <? foreach($_menus_ AS $_selected_ => $_menu_): ?>
  <li<?=($_selected_==$_selected_tab_)?' class="selected"':'';?>><a href="<?=TO_WEB_ROOT . $_menu_['url']?>" title="<?=t($_menu_['title'])?>"><?=t($_menu_['name'])?></a></li>
  <? endforeach; ?>
</ul>