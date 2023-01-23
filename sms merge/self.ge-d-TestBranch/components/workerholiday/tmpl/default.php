<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
$Edit = C::_( 'ID', $this->data );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Save', $this->_option_edit, 'save' );
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<div class="row">
		<div class="col-md-6">
			<?php Xhelp::HelpBox(); ?>
		</div>
		<div class="col-md-6">
			<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
				<?php
				if ( $Edit )
				{
					echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'edit.xml' );
				}
				else
				{
					echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml' );
				}
				?>

				<input type="hidden" value="save" name="task" /> 
			</form>
		</div>
	</div>
</div>
<?php
$this->setHelp();

