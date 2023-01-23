
<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
$mode = 0;
if ( C::_( 'ORG', $this->data ) > 0 )
{
	$mode = 1;
}
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Save', $this->_option_edit, 'save' );
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
//			Helper::getToolbar( 'Generate', $this->_option_edit, 'do', 0, 1 );
//			Helper::getToolbar( 'next', $this->_option_edit, 'next' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<div class="row">
			<div class="col-md-6">
			</div>
			<div class="col-md-6">
				<?php
				if ( C::_( 'Edit', $this ) )
				{
					echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml', 'params', 'orgprint' );
				}
				else
				{
					echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml' );
				}
				if ( $mode == 1 )
				{
					echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml', 'params', 'worker' );
				}
				else
				{
					?>
					<div class="Georgian3 text-center text-danger"><?php echo '<br>' . Text::_( 'Please, Choose ORG!' ); ?></div>
					<?php
				}
				?>
				<input type="hidden" value="save" name="task" /> 
				<input type="hidden" value="1" name="fromdef" /> 
			</div>
		</div>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

