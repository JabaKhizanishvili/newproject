<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$ParamsData = Request::getVar( 'params', array() );
$Workers = (array) Request::getVar( 'nid', C::_( 'WORKERS', $ParamsData, array() ) );
$Data = new stdClass();
$Data->WORKERS = implode( '|', $Workers );
//$Data->GRAPHTYPE = C::_( 'params.GRAPHTYPE', 'request', 0 );
//$Data->GRAPHGROUP = C::_( 'params.GRAPHGROUP', 'request', -1 );
$params = HTML::convertParams( $Data );
?>
<div class="page_title">
	<?php echo Text::_('changing'); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Save', $this->_option_edit, 'save_assignment' );
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<?php
		echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_changing.xml' );
		?>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

