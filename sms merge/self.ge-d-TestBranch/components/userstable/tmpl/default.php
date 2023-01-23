<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
$Dates = Helper::GetDatesFromBillID( $this->data->BILL_ID );
if ( $this->data )
{
	$this->data->WORKERDATA = '';
	if ( $UserData = XGraph::GetOrgUser( $this->data->WORKER ) )
	{
		$this->data->WORKERDATA = $UserData->FIRSTNAME . ' ' . $UserData->LASTNAME;
	}
	$params = HTML::convertParams( $this->data );
}
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
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<div class="col-md-6">
		</div>
		<div class="col-md-6">
			<?php
			echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml' );
			foreach ( $Dates as $Date )
			{
				$DayDate = PDate::Get( $Date );
				$Day = $DayDate->toFormat( '%d' );
				$Month = $DayDate->toFormat( '%B' );
				$Key = 'DAY' . $Day;
				?>
				<div class="form-group" id="form-item-<?php echo $Key; ?>">
					<label class="control-label" for="paramid<?php echo $Key; ?>">
						<label id="paramsFIRSTNAME-lbl" for="params<?php echo $Key; ?>"><?php echo $Day; ?>	<?php echo $Month; ?></label>
						<span class="label-must">
							<i class="bi bi-asterisk form_must_fill"></i>
						</span>
					</label>
					<input type="number" name="params[<?php echo $Key; ?>]" id="params<?php echo $Key; ?>" value="<?php echo C::_( $Key, $this->data ); ?>" step="0.1" class="form-control"/>
				</div>
				<?php
			}
			?>
		</div>
		<input type="hidden" value="<?php // echo ;                            ?>" name="bill_id" /> 
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

