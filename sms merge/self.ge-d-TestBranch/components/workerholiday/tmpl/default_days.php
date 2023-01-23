<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$json = json_encode( $this->data->data );
?>
<div class="page_title">
	<?php echo Text::_( 'Graph days' ); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Save', $this->_option_edit, 'save_rel' );
		Helper::getToolbar( 'Back', $this->_option_edit, 'cancel_rel' );
		?>
	</div>
	<div class="cls"></div>
</div>

<div class="page_content">
	<form action="" method="get" name="fform" id="fform">
		<?php
		echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'default_days.xml', $config );
		?>

		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
		<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
		<input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
		<input type="hidden" value='<?php echo $json; ?>' name="json" />
		<input type="hidden" value="" name="task" />
	</form>
</div>
<?php
$this->setHelp();
