<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
/** @var TableHrs_workersInterface $User */
$User = $this->data->User;
/** @var TableHrs_tableInterface $Table */
$Table = $this->data->items;
$Dates = Helper::GetDatesFromBillID( $this->data->id );
$StartDate = C::_( '0', $Dates );
$EndDate = C::_( '0', array_reverse( $Dates ) );
$config0 = Helper::getConfig( 'htable_identificator' );
?>
<div class="page_title_add">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getJSToolbar( 'Print', 'window.print', array() );
		?>
	</div>
	<div class="cls"></div>
</div>

<div class="page_content container">
	<form action="" method="get" name="fform" id="fform" class="form-horizontal">

		<table border="0" class="table table-bordered text-center">
			<thead>
				<tr>
					<th colspan="10" class="Georgian1 bulletin_report_item_head">
						<?php echo Text::_( 'Worked Howrs Calculation Form' ); ?>
					</th>
				</tr>
				<tr>
					<th colspan="5" class="bulletin_report_item_head"><?php echo Text::_( 'Key' ); ?></th>
					<th  class="bulletin_report_item_head"><?php echo Text::_( 'Value' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="5"><?php echo Text::_( 'Calculation Period' ); ?></td>
					<td><?php echo $StartDate; ?> - <?php echo $EndDate; ?></td>
				</tr>
				<tr>
					<td colspan="5"><?php echo Text::_( 'Worker' ); ?></td>
					<td>
						<?php echo XTranslate::_( $User->FIRSTNAME, 'person' ); ?>
						<?php echo XTranslate::_( $User->LASTNAME, 'person' ); ?>
					</td>
				</tr>
				<tr>
					<td colspan="5"><?php echo Text::_( 'Position' ); ?></td>
					<td >
						<?php echo XTranslate::_( $User->POSITION ); ?>
					</td>
				</tr>
				<tr>
					<td colspan="5"><?php
						if ( $config0 == 0 )
						{
							echo Text::_( 'TABLENUM' );
						}
						else
						{
							echo Text::_( 'PRIVATE_NUMBER' );
						}
						?></td>
					<td>
						<?php
						if ( $config0 == 0 )
						{
							echo $User->TABLENUM;
						}
						else
						{
							echo $User->PRIVATE_NUMBER;
						}
						?>
					</td>
				</tr>
				<?php
				$Month = PDate::Get( $Dates[0] );
				$K = 0;
				$HWItems = HolidayLimitsTable::GetHolidayIDx( 0, 'a' );
				$HWLItems = HolidayLimitsTable::GetHolidayIDx( 1, 'a' );
				foreach ( $Dates as $Date )
				{
					$DayDate = PDate::Get( $Date );
					$D = $DayDate->toFormat( '%d' );
					$DayValue = $Table->{'DAY' . $D};
					?>
					<tr>
						<?php
						if ( $K == 0 )
						{
							?>
							<td rowspan="<?php echo count( $Dates ) + 2; ?>" colspan="4"><?php echo Text::_( 'Hours Marks' ); ?></td>
							<?php
						}
						?>
						<td ><?php echo $D; ?> <?php echo $Month->toFormat( '%B' ); ?></td>
						<td>
							<?php
							if ( $DayValue < 0 )
							{
								$Key = abs( $DayValue ) - 100;
								if ( isset( $HWItems[$Key] ) )
								{
									$DayValue = -100;
								}
								elseif ( isset( $HWLItems[$Key] ) )
								{
									$DayValue = -101;
								}
								switch ( $DayValue )
								{
									case -101:
										echo Text::_( 'WLH' );
										break;
									case -105:
										echo Text::_( 'B' );
										break;
									default:
									case -100:
										echo Text::_( 'H' );
										break;
									case -80:
										echo Text::_( 'G' );
										break;
								}
							}
							elseif ( $DayValue == 0 )
							{
								echo 'X';
							}
							else
							{
								echo $DayValue . ' ' . Text::_( 'Hour' );
							}
							?>
						</td>
					</tr>
					<?php
					if ( $D == 15 )
					{
						?>
						<tr>
							<td ><?php echo Text::_( 'First Half' ); ?></td>
							<td>
								<?php echo $Table->DAYSUM01; ?> <?php echo Text::_( 'Hour' ); ?>
							</td>
						</tr>
						<?php
					}
					$K = 1;
				}
				?>
				<tr>
					<td ><?php echo Text::_( 'Second Half' ); ?></td>
					<td>
						<?php echo $Table->DAYSUM02; ?> <?php echo Text::_( 'Hour' ); ?>
					</td>
				</tr>
				<tr>
					<td rowspan="6"><?php echo Text::_( 'Month Hours Marks' ); ?></td>
					<td colspan="4"><?php echo Text::_( 'Day' ); ?></td>
					<td>
						<?php echo $Table->DAYSUM; ?>
					</td>
				</tr>
				<tr>
					<td rowspan="5"><?php echo Text::_( 'Hour' ); ?></td>
					<td colspan="3" ><?php echo Text::_( 'Summary' ); ?></td>
					<td>
						<?php echo $Table->SUMHOUR; ?> <?php echo Text::_( 'Hour' ); ?>
					</td>
				</tr>
				<tr>
					<td rowspan="4"><?php echo Text::_( 'between them' ); ?></td>
					<td colspan="2"><?php echo Text::_( 'Overtime' ); ?></td>
					<td>
						<?php echo $Table->OVERTIMEHOUR; ?> <?php echo Text::_( 'Hour' ); ?>
					</td>
				</tr>
				<tr>
					<td  colspan="2"><?php echo Text::_( 'Night' ); ?></td>
					<td>
						<?php echo $Table->NIGHTHOUR; ?> <?php echo Text::_( 'Hour' ); ?>
					</td>
				</tr>
				<tr>
					<td  colspan="2"><?php echo Text::_( 'HolidayHours' ); ?></td>
					<td>
						<?php echo $Table->HOLIDAYHOUR; ?> <?php echo Text::_( 'Hour' ); ?>
					</td>
				</tr>
				<tr>
					<td  colspan="2"><?php echo Text::_( 'Other' ); ?></td>
					<td>
						<?php echo $Table->OTHERHOUR; ?> <?php echo Text::_( 'Hour' ); ?>
					</td>
				</tr>


				<tr>
					<td rowspan="4"><?php echo Text::_( 'Month No Work Marks' ); ?></td>
					<td rowspan="4"><?php echo Text::_( 'between them' ); ?></td>
					<td  colspan="3"><?php echo Text::_( 'Bulletin' ); ?></td>
					<td>
						<?php echo $Table->BULLETINS; ?> <?php echo Text::_( 'Day' ); ?>
					</td>
				</tr>
				<tr>
					<td colspan="3"><?php echo Text::_( 'WAGE HOLIDAY' ); ?></td>
					<td>
						<?php echo $Table->HOLIDAY; ?> <?php echo Text::_( 'Day' ); ?>
					</td>
				</tr>
				<tr>
					<td colspan="3"><?php echo Text::_( 'WAGELESS HOLIDAY' ); ?></td>
					<td>
						<?php echo $Table->NHOLIDAY; ?> <?php echo Text::_( 'Day' ); ?>
					</td>
				</tr>
				<tr>
					<td colspan="3"><?php echo Text::_( 'OTHER' ); ?></td>
					<td>
						<?php echo $Table->OTHER; ?> <?php echo Text::_( 'Day' ); ?>
					</td>
				</tr>
				<tr>
					<td colspan="5"><?php echo Text::_( 'HolidayDys' ); ?></td>
					<td>
						<?php echo $Table->HOLIDAYS; ?> <?php echo Text::_( 'Day' ); ?>
					</td>
				</tr>
				<tr>
					<?php ?>
			</tbody>
		</table>
		<?php echo Text::_( 'TABLE_DESC_TEXT' ); ?>
		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();
