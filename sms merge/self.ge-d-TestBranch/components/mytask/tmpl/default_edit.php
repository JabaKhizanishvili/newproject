<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );

$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">

	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
		<form action="?option=<?php echo $this->_option_edit; ?>" method="post" name="fform" id="fform" class="form-horizontal" role="form">
		<div class="wf_data">
			<?php
			echo HTML::renderPage( $this->wdata, dirname( __FILE__ ) . DS . 'view.xml', $config );
			?>
		</div>
		<br />
		<br />
		<?php
		if ( !Collection::get( 'STATE', $this->data ) )
		{
			echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_edit.xml' );
		}
		?>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

