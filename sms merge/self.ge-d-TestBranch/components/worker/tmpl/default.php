<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
$Active = C::_( 'ACTIVE', $this->data );

if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
$Orgs = Units::getOrgList();
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Save', $this->_option_edit, 'save' );
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
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
			<div class="col-md-6">
				<?php
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml' );
				?>
			</div>
			<!--here-->
			<div class="col-md-6">
				<?php
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml', 'params', 'system' );
				?>
			</div>
			<!--here-->
<!--			<div class="col-md-6">
				<ul class="nav nav-tabs nav-justified">
					<?php
					$K = 1;
					foreach ( $Orgs as $Key => $Item )
					{
						$Actual = C::_( 'ENABLE', C::_( $Key, $this->orgdata ) );
						$Active = $K == 1 ? ' class="active" ' : '';
						?>
						<li<?php echo $Active; ?>>
							<a data-toggle="tab" href="#ORG<?php echo $Key; ?>">
								<?php echo C::_( 'TITLE', $Item ); ?>
								<?php
								if ( $Actual )
								{
									?>
									<i class="glyphicon glyphicon-ok-circle green" ></i>								
									<?php
								}
								else
								{
									?>
									<i class="glyphicon glyphicon-remove-circle red" style="color: red;"></i>								
									<?php
								}
								?>
							</a>
						</li>
						<?php
						$K++;
					}
					?>
				</ul>
				<div class="cls"></div>
				<br />
				<div class="tab-content">
					<?php
					$KM = 1;

					foreach ( $Orgs as $Key => $Item )
					{
						$OrgData = C::_( $Key, $this->orgdata, new stdClass() );
						$OrgData->ORG = C::_( 'ORG', $OrgData, $Key );
						$Data = HTML::convertParams( $OrgData );
						$Active = $KM == 1 ? 'in active ' : '';
						?>
						<div id="ORG<?php echo $Key; ?>" class="tab-pane fade <?php echo $Active; ?>">
							<?php echo HTML::renderParams( $Data, dirname( __FILE__ ) . DS . 'org.xml', 'ORG_' . $Key ); ?>
						</div>
						<?php
						$KM++;
					}
					?>
				</div>
			</div>-->
		</div>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

