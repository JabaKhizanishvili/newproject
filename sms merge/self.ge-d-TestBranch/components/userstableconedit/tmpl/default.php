<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
$Dates = Helper::GetDatesFromBillID( $this->data->BILL_ID );
$StartDate = C::_( 0, $Dates );
$EndDate = end( $Dates );

if ( $this->data )
{
	$this->data->WORKERDATA = '';
	$UserData = XGraph::getWorkerDataSch( $this->data->WORKER );
	if ( $UserData )
	{
		$this->data->WORKERDATA = $UserData->FIRSTNAME . ' ' . $UserData->LASTNAME;
	}
	$params = HTML::convertParams( $this->data );
}
$XTable = new XHRSTable();
$model = $this->getModel( $params );
$Apps = $model->GetAPPS( $this->data->WORKER, $StartDate, $EndDate );
$Now = PDate::Get()->toUnix();
$WorkerData = C::_( 'items.' . $this->data->WORKER, $XTable->GetWorkersData( $StartDate, $EndDate, array( $this->data->WORKER ), $this->data->ORG ) );
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
				$TimeID = (int) C::_( $Date . '.TIME_ID', $WorkerData, 0 );
				$DayDate = PDate::Get( $Date );
				$Day = $DayDate->toFormat( '%d' );
				$Month = $DayDate->toFormat( '%B' );
				if ( $DayDate->toUnix() > $Now )
				{
					continue;
				}
				$Key = 'DAY' . $Day;
				$AppType = C::_( $DayDate->toFormat( '%Y-%m-%d' ) . '.TYPE', $Apps, -9999 );
				$Value = C::_( $Key, $this->data );

				$App = C::_( $DayDate->toFormat( '%Y-%m-%d' ), $Apps, null );
				if ( isset( $App ) && (($AppType != 5 && $TimeID > 0) || ($AppType == 5) || $Value == -80) )
				{
					$result = 100 + C::_( 'TYPE', $App );
					$title = XTranslate::_( C::_( 'TITLE', $App ) );
					if ( $Value == -80 )
					{
						$title = Text::_( 'missing' );
					}
					?>
					<div class="form-group" id="form-item-<?php echo $Key; ?>">
						<label class="control-label" for="paramid<?php echo $Key; ?>">
							<label id="paramsFIRSTNAME-lbl" for="params<?php echo $Key; ?>"><?php echo $Day; ?>	<?php echo $Month; ?></label>
							<span class="label-must">
								<i class="bi bi-asterisk form_must_fill"></i>
							</span>
						</label>
						<input type="hidden" name="params[<?php echo $Key; ?>]" id="params<?php echo $Key; ?>" value="-<?php echo $result; ?>" class="form-control" step="0.1">
						<div class="form-control form_field">
							<?php echo $title; ?>
						</div>
					</div>

					<?php
				}
				else
				{
					?>
					<div class="form-group" id="form-item-<?php echo $Key; ?>">
						<label class="control-label" for="paramid<?php echo $Key; ?>">
							<label id="paramsFIRSTNAME-lbl" for="params<?php echo $Key; ?>"><?php echo $Day; ?>	<?php echo $Month; ?></label>
							<span class="label-must">
								<i class="bi bi-asterisk form_must_fill"></i>
							</span>
						</label>
						<input type="number" name="params[<?php echo $Key; ?>]" id="params<?php echo $Key; ?>" value="<?php echo C::_( $Key, $this->data ); ?>" class="form-control" step="0.1">
					</div>
					<?php
				}
			}
			?>
			<br />
			<br />
			<br />

			<?php
			$Collect = [
					'OVERTIMEHOUR',
					'NIGHTHOUR',
					'HOLIDAYHOUR',
					'OTHERHOUR',
					'OTHER'
			];

			foreach ( $Collect as $Key )
			{
				?>
				<div class="form-group" id="form-item-<?php echo $Key; ?>">
					<label class="control-label" for="paramid<?php echo $Key; ?>">
						<label id="paramsFIRSTNAME-lbl" for="params<?php echo $Key; ?>">
							<?php echo Text::_( $Key . '_KEY' ); ?>
						</label>
						<span class="label-must">
							<i class="bi bi-asterisk form_must_fill"></i>
						</span>
					</label>
					<input type="number" name="params[<?php echo $Key; ?>]" id="params<?php echo $Key; ?>" value="<?php echo C::_( $Key, $this->data ); ?>" class="form-control" step="0.1">
				</div>
			<?php } ?>

			<input type="hidden" value="save" name="task" /> 
		</div>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

