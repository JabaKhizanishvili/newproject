<?php
defined('PATH_BASE') or die('Restricted access');
/* @var $menu MenuConfig */
$menu = MenuConfig::getInstance();
$active = $menu->getActive();
?>
<div class="page_title">
    <?php echo Helper::getPageTitle(); ?>
</div>