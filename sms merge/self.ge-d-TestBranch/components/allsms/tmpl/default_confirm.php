<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Edit', $this->_option_edit, 'edit' );
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		Helper::getToolbar( 'Confirm', $this->_option_edit, 'confirm' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<?php
		echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_confirm.xml' );
		?>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();
