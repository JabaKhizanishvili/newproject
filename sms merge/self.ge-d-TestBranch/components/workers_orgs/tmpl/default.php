<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'New', $this->_option_edit );
		Helper::getToolbar( 'Changing', $this->_option_edit, 'changing', 1 );
		Helper::getToolbar( 'Release', $this->_option_edit, 'release', 1, 1 );

//		Helper::getToolbar( 'Reset Password', $this->_option_edit, 'passwordresset', 1, 1 );
//		Helper::getToolbar( 'Unset', $this->_option_edit, 'unset', 1, 1 );
//		Helper::getToolbar( 'Assignment', $this->_option_edit, 'assignment', 1 );
//		Helper::getToolbar( 'Edit', $this->_option_edit, '', 1 );
//		Helper::getToolbar( 'Change Role', $this->_option_edit, 'changerole', 1 );
//		Helper::getToolbar( 'Release', $this->_option_edit, 'release', 1, 1 );
//		Helper::getToolbar( 'Change Category', $this->_option_edit, 'changecategory', 1 );

//		Helper::getToolbar( 'Change AD Role', $this->_option_edit, 'changeadrole', 1 );
//		Helper::getToolbar( 'Change Graph', $this->_option_edit, 'changegraph', 1 );
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
