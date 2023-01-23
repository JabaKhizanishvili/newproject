<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$Layout = Helper::getConfig( 'transacton_report_show_pictures', 0 );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Confirm', $this->_option_edit, 'confirm', 1 );
		Helper::getToolbar( 'Edit', $this->_option_edit, '', 1 );
		Helper::getToolbar( 'Delete', $this->_option_edit, 'delete', 1, 1 );
//		Helper::getJSToolbar( 'Print', 'window.print', array() );
		Helper::getToolbar( 'Export To Exel', $this->_option, 'export' );
		?>
	</div>
	<div class="cls"></div>
</div>

<div class="page_content">
	<form action="" method="get" name="fform" id="fform" class="form-horizontal">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
//		if ( count( $this->data->items ) )
//		{
		?>
		<div class="report_page_result">
			<?php
			echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'default.xml', $config );
			?>
		</div>
		<?php
//		}
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
