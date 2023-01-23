<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
$task = Request::getVar( 'task' );
$set_task = $task == 'new' ? 'save_new' : 'save';
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Save', $this->_option_edit, $set_task );
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<div class="row">
			<div class="col-md-6">
				<?php
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml' );
				?>
			</div>
			<div class="col-md-6">
				<?php
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml', 'params', 'add' );
				?>
			</div>
		</div>
		<input type="hidden" value="<?php echo Request::getVar( 'lng' ); ?>" name="lng" />
		<input type="hidden" value="<?php echo Request::getVar( 'hash' ); ?>" name="hash" />
		<input type="hidden" value="<?php echo $set_task; ?>" name="task" />
	</form>
</div>
<?php
$this->setHelp();

