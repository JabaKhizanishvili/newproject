<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$stop = C::_( 'data.stop', $this );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
//		Helper::getToolbar( 'New', $this->_option_edit );
//		Helper::getToolbar( 'Edit', $this->_option_edit, '', 1 );
//		Helper::getToolbar( 'Copy', $this->_option_edit, 'copydata', 1, 1 );
//		Helper::getToolbar( 'Delete', $this->_option_edit, 'delete', 1, 1 );
		Helper::getToolbar( 'Daily Record (manual testing)', $this->_option_edit, 'record', 0, 1 );
		Helper::getToolbar( 'Generation', $this->_option_edit, 'generation', 0, 1 );
		?>
	</div>
	<div class="cls"></div>
</div>

<div class="page_content">
	<form action="" method="get" name="fform" id="fform">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
		if ( $stop != 0 )
		{
			?>
			<br><br><br><br><div class="Georgian3 text-center text-danger"><?php echo Text::_( 'PLEASE, CHOOSE ORG AND PERIOD!' ); ?></div>
			<?php
		}
		else
		{
			echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'default.xml', $config );
		}
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
