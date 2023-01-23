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
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		Helper::getToolbar( 'next', $this->_option_edit, 'nextstep' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<div class="col-md-6">
			<?php
			echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml' );
			?>
		</div>
		<input type="hidden" value="" name="task" />
	</form>
</div>
<?php
$this->setHelp();
