<?php
defined('PATH_BASE') or die('Restricted access');
?>
<div class="page_title">
    <?php echo Helper::getPageTitle(); ?>
    <div class="cls"></div>
</div>
<div class="page_content">
    <div class="message noscript">
        <?php echo Text::_('Request Done!'); ?>
    </div>
</div>
<?php
$this->setHelp();

