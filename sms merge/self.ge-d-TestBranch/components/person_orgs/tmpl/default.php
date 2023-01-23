<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Assignment', $this->_option_edit, 'assignment' );
		Helper::getToolbar( 'Changing', $this->_option_edit, 'changing', 1 );
		Helper::getToolbar( 'ScheduleChanging', $this->_option_edit, 'schedulechanging', 1 );
		Helper::getToolbar( 'Release', $this->_option_edit, 'release', 1, 1 );
		if ( PAYROLL == 1 )
		{
			Helper::getToolbar( 'manage_benefits', $this->_option_edit, 'benefits', 1, 1 );
		}
		Helper::getToolbar( 'Rollback', $this->_option_edit, 'rollback', 1, 1 );
		Helper::getToolbarExport( 'Export To Exel', $this->_option, 'export', 0, 0 );
		?>
	</div>
	<div class="cls"></div>
</div>

<div class="page_content">
	<form action="" method="get" name="fform" id="fform">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
		echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'default.xml', $config );
		?>

		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
		<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
		<input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
		<input type="hidden" value="" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();
