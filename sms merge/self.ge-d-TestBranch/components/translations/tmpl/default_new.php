<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$lng = C::_( 'target_lng', $this->data );
$count = C::_( 'items', $this->data );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Cancel', $this->_option, 'go_back' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="" method="get" class="form-horizontal" name="fform" id="fform">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default_new.xml', $config );
		if ( count( $count ) > 0 )
		{
			echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'default_new.xml', $config );
		}

		if ( empty( $lng ) )
		{
			?>
			<br><br><br><br><div class="Georgian3 text-center text-danger"><?php echo Text::_( 'PLEASE, TARGET LANGUAGE!' ); ?></div><br><br><br>
		<?php } ?>

		<input type = "hidden" value = "<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name = "option" />
		<input type = "hidden" value = "<?php echo $this->data->order; ?>" name = "order" id = "order" />
		<input type = "hidden" value = "<?php echo $this->data->dir; ?>" name = "dir" id = "dir"/>
		<input type = "hidden" value = "<?php echo $this->data->start; ?>" name = "start" id = "start"/>
		<input type = "hidden" value = "<?php echo Request::getVar( 'task', 'start_new' ); ?>" name = "task" />
		<input type = "hidden" value = "1" name = "set" id = "set"/>
	</form>
</div>
<?php
$this->setHelp();
