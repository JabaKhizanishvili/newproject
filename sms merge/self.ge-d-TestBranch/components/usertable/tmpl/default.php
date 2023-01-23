<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
$Dates = Helper::GetDatesFromBillID( $this->data->BILL_ID );
if ( $this->data )
{
	$UserData = Users::getUser( $this->data->WORKER );
	$this->data->WORKERDATA = $UserData->FIRSTNAME . ' ' . $UserData->LASTNAME;
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
				<label class="control-label col-sm-5" for="paramid<?php echo $Key; ?>">
					<?php echo $Day; ?>	<?php echo $Month; ?>
				</label>
				<div class="col-sm-7">
					<div style="display: block; width: 100%; float: none;position: relative;">
						<div class="input-group">
							<input type="number" name="params[<?php echo $Key; ?>]" id="params<?php echo $Key; ?>" value="<?php echo C::_( $Key, $this->data ); ?>" step="0.1" class="form-control"/>
							<span class="input-group-addon">
								<i class="bi bi-asterisk form_must_fill"></i>
							</span>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		<input type="hidden" value="<?php // echo ;                      ?>" name="bill_id" /> 
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

