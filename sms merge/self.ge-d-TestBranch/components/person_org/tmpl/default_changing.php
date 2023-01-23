<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$ParamsData = Request::getVar( 'params', array() );
$Workers = (array) Request::getVar( 'nid', C::_( 'WORKERS', $ParamsData, array() ) );
$Data = new stdClass();
$Data->WORKERS = implode( '|', $Workers );
$Data->ORG = C::_( 'ORG', $this->data );
if ( count( $Workers ) == 1 )
{
	$Data = $this->data;
	$Data->WORKERS = C::_( 'ID', $Data );
}

$params = HTML::convertParams( $Data );
$page_title = Text::_( 'changing' );
?>
<div class="page_title">
	<?php echo $page_title; ?>
	<div class="toolbar">
		<?php
//		Helper::getToolbar( 'Save', $this->_option_edit, 'save_change' );
		Xhelp::Confirmation( $this->_option_edit, $page_title, 'save_changing' );
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
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_changing.xml' );
				?>
			</div>
			<div class="col-md-6">
				<?php
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_changing.xml', 'params', 'more' );
				?>
			</div>
		</div>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();
