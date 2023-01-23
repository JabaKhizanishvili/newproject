<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
/** @var TableHrs_workersInterface $User */
//$User = $this->data->User;
/** @var TableHrs_tableInterface $Table */
?>
<div class="page_content table-page">
	<form action="" method="get" name="fform" id="fform" class="form-horizontal">
		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="" name="task" /> 
		<div class="page_title">
			<?php // echo Helper::getPageTitle();  ?>
			<div class="toolbar">
				<?php
				Helper::getJSToolbar( 'Print', 'window.print', array() );
				Helper::getJSToolbar( 'Export To Exel', 'exportTableToExcel', array( '#exportable' ) );
				?>
			</div>
			<div class="cls"></div>
		</div>

		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
		if ( $this->data->bill_id && $this->data->loaded == 1 )
		{
			$Dates = Helper::GetDatesFromBillID( $this->data->bill_id );
			$StartDate = C::_( '0', $Dates );
			$EndDate = C::_( '0', array_reverse( $Dates ) );
			?>
			<div class="table_form_title">
				<?php echo Text::_( 'TIMECALCFORM' ); ?>
			</div>
			<div class="table_org_title">
				<?php echo Helper::getConfig( 'system_org' ); ?>
			</div>
			<div class="table_title_desc">
				<?php echo Text::_( 'ORGNAME' ); ?>
			</div>
			<div class="table_org_title">
				<?php
				echo C::_( $this->data->unit . '.TITLE', $this->data->UNITS );
				?>
			</div>
			<div class="table_title_desc">
				<?php echo Text::_( 'CHAPTER' ); ?>
			</div>
			<div class="row c-row">
				<div class="col-md-4">
					<table class="table-add-tables">
						<tbody>
							<tr>	
								<td><?php echo Text::_( 'TIN' ); ?></td>
								<td><?php echo Helper::getConfig( 'system_tin' ); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-md-4  text-right">
					<table class="table-add-tables table-add-tables-right">
						<tbody>
							<tr>	
								<td><?php echo Text::_( 'CDATE' ); ?></td>
								<td><?php echo PDate::Get( $StartDate )->toFormat( '10 %B, %Y' ) ?> <?php echo Text::_( 'Y' ); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-md-4">
					<table class="table-add-tables table-add-tables-right">
						<tbody>
							<tr>	
								<td colspan="2"><?php echo Text::_( 'Calculation Period' ); ?></td>
								<td><?php echo $StartDate; ?><?php echo Text::_( 'FROM' ); ?></td>
								<td><?php echo $EndDate; ?><?php echo Text::_( 'To' ); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<table border="0" class="table table-bordered text-center table-condensed table-small-font table-responsive" id="exportable">
				<thead>
					<tr>
					<tr>
						<th rowspan="4" ><?php echo Text::_( '#' ); ?></th>
						<th rowspan="4" ><?php echo Text::_( 'Worker-Position' ); ?></th>
						<th rowspan="4" ><?php echo Text::_( 't-p Number' ); ?></th>
						<th  rowspan="1" colspan="<?php echo (Count( $Dates ) + 2) ?>"><?php echo Text::_( 'Hours Marks' ); ?></th>
						<th colspan="6"><?php echo Text::_( 'Month Hours Marks' ); ?></th>
						<th colspan="4"><?php echo Text::_( 'Month No Work Marks' ); ?></th>
						<th  rowspan="4"><?php echo Text::_( 'HolidayDys' ); ?></th>
						<th  rowspan="4"><?php echo Text::_( 'approver' ); ?></th>
						<th  rowspan="4"><?php echo Text::_( 'Aprove Date' ); ?></th>
					</tr>
					<tr>
						<?php
						$K = 0;
						$Table = C::_( '0', $this->data->items );
						$Month = PDate::Get( $Dates[0] );
						foreach ( $Dates as $Date )
						{
							$DayDate = PDate::Get( $Date );
							$D = $DayDate->toFormat( '%d' );
							?>
							<th  rowspan="3" ><?php echo (int) $D; ?> <?php echo $Month->toFormat( '%b' ); ?></th>
							<?php
							if ( $D == 15 )
							{
								?>
								<th  rowspan="3" ><?php echo Text::_( 'First Half' ); ?></th>
								<?php
							}
							$K = 1;
						}
						?>
						<th rowspan="3"><?php echo Text::_( 'Second Half' ); ?></th>
						<th rowspan="3"><?php echo Text::_( 'Day' ); ?></th>
						<th colspan="5"><?php echo Text::_( 'Hour' ); ?></th>
						<th colspan="4"><?php echo Text::_( 'between them' ); ?></th>
					</tr>
					<tr>
						<th rowspan="2"><?php echo Text::_( 'SUMMARY' ); ?></th>
						<th colspan="4"><?php echo Text::_( 'between them' ); ?></th>
						<th rowspan="2"><?php echo Text::_( 'Bulletin' ); ?></th>
						<th rowspan="2"><?php echo Text::_( 'WAGE HOLIDAY' ); ?></th>
						<th rowspan="2"><?php echo Text::_( 'WAGELESS HOLIDAY' ); ?></th>
						<th rowspan="2"><?php echo Text::_( 'OTHER' ); ?></th>
					</tr>
					<tr>
						<th><?php echo Text::_( 'Overtime' ); ?></th>
						<th><?php echo Text::_( 'Night' ); ?></th>
						<th><?php echo Text::_( 'HolidayHours' ); ?></th>
						<th><?php echo Text::_( 'Other' ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="50"	class="text-left">
							<strong><?php echo Text::_( 'TABLE_DESC' ); ?></strong>
							<?php echo Text::_( 'TABLE_DESC_TEXT' ); ?>
						</td>
					</tr>
					<?php
					$total = isset( $this->data->total ) ? $this->data->total : '';
					$start = isset( $this->data->start ) ? $this->data->start : '';
					$paging = Pagination::Generate( $total, $start );
					if ( empty( $total ) )
					{
						return;
					}
					?>
					<tr>
						<td colspan="50">
							<div class="footer_block">
								<?php
								if ( !empty( $paging ) )
								{
									echo $paging;
								}
								?>		
							</div>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$K = $this->data->start + 1;
					foreach ( $this->data->items as $Worker )
					{
						?>
						<tr>
							<td><?php echo $K; ?></td>
							<td class="text-left">
								<strong>
									<?php echo C::_( 'WORKERNAME', $Worker ); ?><br />
								</strong>
								<span class="table-position text-nowrap">
									<?php echo C::_( 'POSITION', $Worker ); ?>
								</span>
							</td>
							<td><?php echo empty( C::_( 'TABLENUM', $Worker, '' ) ) ? C::_( 'PRIVATE_NUMBER', $Worker ) : C::_( 'TABLENUM', $Worker ); ?></td>
							<?php
							foreach ( $Dates as $Date )
							{
								$DayDate = PDate::Get( $Date );
								$D = $DayDate->toFormat( '%d' );
								$DayValue = C::_( 'DAY' . $D, $Worker );
								?>
								<td>
									<?php
									if ( $DayValue < 0 )
									{
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
										}
									}
									elseif ( $DayValue == 0 )
									{
										echo 'X';
									}
									else
									{
										echo $DayValue . ' ' . Text::_( 'THOUR' );
									}
									?>
								</td>
								<?php
								if ( $D == 15 )
								{
									?>
									<td><?php echo C::_( 'DAYSUM01', $Worker ) . ' ' . Text::_( 'THOUR' ); ?></td>
									<?php
								}
							}
							?>
							<td><?php echo C::_( 'DAYSUM02', $Worker ) . ' ' . Text::_( 'THOUR' ); ?></td>
							<td><?php echo C::_( 'DAYSUM', $Worker ); ?></td>
							<td><?php echo C::_( 'SUMHOUR', $Worker ) . ' ' . Text::_( 'THOUR' ); ?></td>
							<td><?php echo C::_( 'OVERTIMEHOUR', $Worker ) . ' ' . Text::_( 'THOUR' ); ?></td>
							<td><?php echo C::_( 'NIGHTHOUR', $Worker ) . ' ' . Text::_( 'THOUR' ); ?></td>
							<td><?php echo C::_( 'HOLIDAYHOUR', $Worker ) . ' ' . Text::_( 'THOUR' ); ?></td>
							<td><?php echo C::_( 'OTHERHOUR', $Worker ) . ' ' . Text::_( 'THOUR' ); ?></td>
							<td><?php echo C::_( 'BULLETINS', $Worker ) . ' ' . Text::_( 'Day' ); ?></td>
							<td><?php echo C::_( 'HOLIDAY', $Worker ) . ' ' . Text::_( 'Day' ); ?></td>
							<td><?php echo C::_( 'NHOLIDAY', $Worker ) . ' ' . Text::_( 'Day' ); ?></td>
							<td><?php echo C::_( 'OTHER', $Worker ) . ' ' . Text::_( 'Day' ); ?></td>
							<td><?php echo C::_( 'HOLIDAYS', $Worker ) . ' ' . Text::_( 'Day' ); ?></td>
							<td>
								<?php echo C::_( 'CHIEFNAME', $Worker ); ?>
							</td>
							<td>
								<?php
								$Date = C::_( 'APPROVE_DATE', $Worker );
								if ( $Date )
								{
									echo PDate::Get( $Date )->toFormat( '%d-%m-%Y' );
								}
								?>
							</td>

						</tr>
						<?php
						$K++;
					}
					?>
				</tbody>
			</table>
			<?php
		}
		else if ( $this->data->loaded == 2 )
		{
			?>
			<div class="Georgian3 text-center text-danger"><?php echo Text::_( 'no data found!' ); ?></div>
			<?php
		}
		else
		{
			?>
			<div class="Georgian3 text-center text-danger"><?php echo Text::_( 'Please, Enter Bill ID!' ); ?></div>
			<?php
		}
		?>
	</form>
</div>

