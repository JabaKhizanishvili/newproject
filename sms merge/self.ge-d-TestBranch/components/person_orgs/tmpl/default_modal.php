<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$hist_date = C::_( 'hist_date', $this->data );
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

<div class="page_content">
	<form action="" method="get" name="fform" id="fform">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default_modal.xml', $config );
		echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'default_modal.xml', $config );
		?>

		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="<?php echo Request::getVar( 'layout', 'modal' ); ?>" name="layout" />
		<input type="hidden" value="<?php echo Request::getVar( 'tmpl', 'modal' ); ?>" name="tmpl" />
		<input type="hidden" value="<?php echo $hist_date; ?>" name="date" id="date" />
		<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
		<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
		<input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
		<input type="hidden" value="" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();
