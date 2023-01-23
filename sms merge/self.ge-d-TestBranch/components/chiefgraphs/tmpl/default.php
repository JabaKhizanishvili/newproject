<?php
defined('PATH_BASE') or die('Restricted access');
$config = get_object_vars($this);
require_once PATH_BASE . DS . 'libraries' . DS . 'calendarhelper.php';
TKCalendar::$step = 10;
$params = '';
if($this->data)
{
    $params = HTML::convertParams($this->data);
}
?>
<div class="page_title">
    <?php echo Helper::getPageTitle(); ?>
    <div class="cls"></div>
</div>
<div class="page_content">  
    <form action="?option=<?php echo $this->_option; ?>" method="post" class="form-horizontal" name="fform" id="fform">
        <?php
        require 'current.php';
        ?>
        <div style = "display: none;">
            <input type = "submit" />
        </div>
    </form>
</div>
<?php
$this->setHelp();
