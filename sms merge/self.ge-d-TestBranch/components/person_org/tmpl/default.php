<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
$Active = C::_( 'ACTIVE', $this->data );

if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
$Orgs = Units::getOrgList();
$mode = 0;
if ( C::_( 'ORG', $this->data ) > 0 )
{
	$mode = 1;
}
$page_title = Text::_( 'Assignment' );
$saveAndAssign = Request::getVar('task') === 'save_and_assign';
?>
<div class="page_title">
	<?php echo $page_title; ?>
	<div class="toolbar">
		<?php
//		Helper::getToolbar( 'Save', $this->_option_edit, 'save' );
		Xhelp::Confirmation( $this->_option_edit, $page_title, 'save_assignment' );
        if ($saveAndAssign) {
            Helper::getToolbar( 'Miss', $this->_option_edit, 'miss' );
        } else {
            Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
        }

		if ( $Active == -2 )
		{
			Helper::getToolbar( 'Delete', $this->_option_edit, 'fulldelete', 0, 1 );
		}
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<div class="row">
			<div class = "col-md-6">
				<?php
				if ( $mode == 1 )
				{
					echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml' );
				}
				else
				{
					echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml', 'params', 'org' );
					?>
					<div class="Georgian3 text-center text-danger"><?php echo '<br>' . Text::_( 'Please, Choose ORG!' ); ?></div>
					<?php
				}
				?>
			</div>
			<div class="col-md-6">
				<?php
				if ( $mode == 1 )
				{
					echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml', 'params', 'more' );
				}
				?>
			</div>
		</div>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();


