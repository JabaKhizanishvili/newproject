<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
$File = X_PATH_BASE . DS . 'components' . DS . $this->C . DS . 'configuration.xml';
if ( !is_file( $File ) )
{
	$link = '?option=' . $this->_option;
	XError::setError( 'data_incorrect' );
	Users::Redirect( $link );
}
$XMLDoc = Helper::loadXMLFile( $File );
$Name = $XMLDoc->getElementByPath( 'params' )->attributes( 'name' );
$PageTitle = Text::_( $Name );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle( $PageTitle ); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Save', $this->_option_edit, 'save' );
		Helper::getToolbar( 'Apply', $this->_option_edit, 'apply' );
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<div class="row">
			<div class="col-md-6 col-lg-6">
				<?php
				if ( $this->C )
				{
					echo HTML::renderParams( $params, X_PATH_BASE . DS . 'components' . DS . $this->C . DS . 'configuration.xml' );
				}
				?>
			</div>
			<div class="col-md-6 col-lg-6">
				<?php
				if ( $this->C )
				{
					echo HTML::renderParams( $params, X_PATH_BASE . DS . 'components' . DS . $this->C . DS . 'configuration.xml', 'params', 'details' );
				}
				?>
			</div>
		</div>
		<input type="hidden" value="save" name="task" /> 
		<input type="hidden" value="<?php echo Request::getCmd( 'c' ); ?>" name="c" /> 
		<input type="hidden" value="<?php echo Request::getCmd( 'tmpl' ); ?>" name="tmpl" /> 
	</form>
</div>
<?php
$this->setHelp();

