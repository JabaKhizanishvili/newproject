<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
$id = C::_( 'ID', $this->data );
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Generation', $this->_option_edit, 'save' );
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
				if ( $id > 0 )
				{
					echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml', 'params', 'print' );
				}
				else
				{
					echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml', 'params', 'default' );
				}
				?>
			</div>
			<div class="col-md-6">
				<?php
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml', 'params', 'details' );
				?>
			</div>
		</div>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

