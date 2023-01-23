<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
$task = C::_( 'OPTON_TASK', $this->data, '' );
if ( $task )
{
	unset( $this->data->OPTON_TASK );
}

if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		if ( $task == 'edit' )
		{
			Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		}
		else
		{
			Helper::getToolbar( 'Prev', $this->_option_edit, 'prev' );
		}
		Helper::getToolbar( 'Next', $this->_option_edit, 'nextstep2' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<div class="col-md-6">
			<?php
			echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_periodcost.xml' );
			?>
		</div>
		<input type="hidden" value="" name="task" />
	</form>
</div>
<?php
$this->setHelp();

