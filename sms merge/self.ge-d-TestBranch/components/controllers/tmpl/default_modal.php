<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$device_id = C::_( 'device_id', $this->data );
$action = C::_( 'action', $this->data );
$this->Set( 'disable_config', true );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
//		Helper::getToolbar( 'Assignment', $this->_option_edit, 'assignment' );
//		Helper::getToolbar( 'Changing', $this->_option_edit, 'changing', 1 );
//		Helper::getToolbar( 'Release', $this->_option_edit, 'release', 1, 1 );
		?>
	</div>
	<div class="cls"></div>
</div>

<div>
	<div class="page_content">
		<form action="" method="get" name="fform" id="fform">
			<?php
//		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default_modal.xml', $config );
			if ( $action == 'monitor' )
			{
				echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'default_monitor.xml', $config );
			}
			else
			{
				echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'default_modal.xml', $config );
			}
			?>

			<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
			<input type="hidden" value="<?php echo Request::getVar( 'layout', 'modal' ); ?>" name="layout" />
			<input type="hidden" value="<?php echo Request::getVar( 'tmpl', 'modal' ); ?>" name="tmpl" />
			<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
			<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
			<input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
			<input type="hidden" value="<?php echo $device_id ?>" name="device_id" /> 
			<input type="hidden" value="<?php echo $action ?>" name="action" /> 
			<input type="hidden" value="" name="task" /> 
		</form>
	</div>
</div>
<?php
$this->setHelp();
