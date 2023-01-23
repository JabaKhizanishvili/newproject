<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
//		Helper::getToolbar( 'New', $this->_option_edit );
		Helper::getToolbar( 'Edit', $this->_option_edit, '', 1 );
		Helper::getToolbar( 'Period Edit', $this->_option_edit, 'periodedit', 0, 0 );
		Helper::getToolbar( 'Delete', $this->_option_edit, 'delete', 1, 1 );

		Helper::getToolbarExport( 'Export To Exel', $this->_option, 'export', 0, 0 );
		Helper::getToolbar( 'Generation', $this->_option_edit, 'generation', 0, 1 );
		Helper::getToolbar( 'Calculate Limit', $this->_option_edit, 'trigger_holidays_cron', 0, 0 );
		Helper::getToolbar( 'Next Preiod Generation', $this->_option_edit, 'nextgeneration', 0, 0 );
		?>
	</div>
	<div class="cls"></div>
</div>

<div class="page_content">
	<form action="" method="get" name="fform" id="fform">
		<?php
		$xml_file = 'default.xml';

		if ( Helper::getConfig( 'show_private_number_in_vacation_limits' ) )
		{
			$xml_file = 'default_private_number.xml';
		}

		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . $xml_file, $config );
		echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . $xml_file, $config );
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
