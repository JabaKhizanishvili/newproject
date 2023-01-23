<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
/** @var TableHrs_workersInterface $User */
//$User = $this->data->User;
/** @var TableHrs_tableInterface $Table */
$config0 = Helper::getConfig( 'htable_identificator' );
$HWItems = HolidayLimitsTable::GetHolidayIDx( 0, 'a' );
$HWLItems = HolidayLimitsTable::GetHolidayIDx( 1, 'a' );
?>
<div class="page_content table-page">
	<form action="" method="get" name="fform" id="fform" class="form-horizontal">
		<div class="page_title">
			<?php echo Helper::getPageTitle(); ?>
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
		if ( $this->data->loaded )
		{
			$OrgName = XTranslate::_( C::_( 'LIB_TITLE', $this->data->ORGDATA ) );
			$TIN = C::_( 'LIB_TIN', $this->data->ORGDATA );
			$Dates = Helper::GetDatesFromBillID( $this->data->bill_id );
			$StartDate = C::_( '0', $Dates );
			$EndDate = C::_( '0', array_reverse( $Dates ) );
			?>
			<div class="table_form_title">
				<?php echo Text::_( 'TIMECALCFORM' ); ?>
			</div>
			<div class="table_org_title">
				<?php echo $OrgName; ?>
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
								<td><?php echo $TIN; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-md-4  text-right">
					<table class="table-add-tables table-add-tables-right">
						<tbody>
							<tr>	
								<td><?php echo Text::_( 'CDATE' ); ?></td>
								<td><?php echo PDate::Get( PDate::Get( $EndDate )->toUnix() + 15 * 24 * 60 * 60 )->toFormat( '10 %B, %Y' ) ?> <?php echo Text::_( 'Y' ); ?></td>
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
			<div class="FloatingScrollbar">
				<table class="table table-bordered text-center table-condensed table-small-font table-responsive" id="exportable">
					<thead>
						<tr class="bulletin_report_item_head">
							<th rowspan="4" ><?php echo Text::_( '#' ); ?></th>
							<th rowspan="4" ><?php echo Text::_( 'Worker' ); ?></th>
							<th rowspan="4" ><?php echo Text::_( 'Position' ); ?></th>
							<th rowspan="4" ><?php echo Text::_( 't-p Number' ); ?></th>
							<th  rowspan="1" colspan="<?php echo (Count( $Dates ) ) ?>"><?php echo Text::_( 'Hours Marks' ) . ' (' . Text::_( 'THOUR' ) . ')'; ?></th>
							<th colspan="6"><?php echo Text::_( 'Month Hours Marks' ); ?></th>
							<th  rowspan="4"><?php echo Text::_( 'approver' ); ?></th>
							<th  rowspan="4"><?php echo Text::_( 'Aprove Date' ); ?></th>
						</tr>
						<tr  class="bulletin_report_item_head">
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
								$K = 1;
							}
							?>
							<th rowspan="3"><?php echo Text::_( 'Day' ); ?></th>
							<th colspan="5"><?php echo Text::_( 'Hour' ); ?></th>
						</tr>
						<tr  class="bulletin_report_item_head">
							<th rowspan="2"><?php echo Text::_( 'SUMMARY' ); ?></th>
							<th colspan="4"><?php echo Text::_( 'between them' ); ?></th>
						</tr>
						<tr  class="bulletin_report_item_head">
							<th><?php echo Text::_( 'Overtime' ); ?></th>
							<th><?php echo Text::_( 'Night' ); ?></th>
							<th><?php echo Text::_( 'HolidayHours' ); ?></th>
							<th><?php echo Text::_( 'Other' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="50"	class="text-left">
								<div class="table_org_title">
									<br />
									<br />
								</div>
								<div class="table_title_desc">
									<?php echo Text::_( 'ORG-DEP CHIEF' ); ?>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="50"	class="text-left">
								<div class="table_org_title">
									<br />
									<br />
								</div>
								<div class="table_title_desc">
									<?php echo Text::_( 'Tabel Creator' ); ?>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="50"	class="text-left">
								<br />
								<strong><?php echo Text::_( 'TABLE_DESC' ); ?></strong>
								<?php echo Text::_( 'TABLE_DESC_TEXT_NEW' ); ?>
							</td>
						</tr>
						<?php
						$total = isset( $this->data->total ) ? $this->data->total : '';
						$start = isset( $this->data->start ) ? $this->data->start : '';
						$paging = Pagination::Generate( $total, $start );
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
										<?php echo XTranslate::_( C::_( 'WORKERNAME', $Worker ), 'person' ) . ' ' . XTranslate::_( C::_( 'WLASTNAME', $Worker ), 'person' ); ?><br />
									</strong>
									<!--<span class="table-position text-nowrap">-->
									<?php
//									echo C::_( 'POSITION', $Worker ); 
									?>
									<!--</span>-->
								</td>
								<td class="text-left">
									<span class="table-position text-nowrap">
										<?php echo XTranslate::_( C::_( 'POSITION', $Worker ) ); ?>
									</span>
								</td>
								<td>
									<?php
									if ( $config0 == 0 )
									{
										echo "'" . C::_( 'TABLENUM', $Worker, '' );
									}
									else
									{
										echo "'" . C::_( 'PRIVATE_NUMBER', $Worker );
									}
									?>
								</td>
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
													echo Text::_( 'WLH_NEW' );
													break;
												case -105:
													echo Text::_( 'B' );
													break;
												case -103:
													echo Text::_( 'D' );
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
											echo $DayValue;
										}
										?>
									</td>
									<?php
								}
								?>
								<td><?php echo C::_( 'DAYSUM', $Worker ); ?></td>
								<td><?php echo C::_( 'SUMHOUR', $Worker ); ?></td>
								<td><?php echo C::_( 'OVERTIMEHOUR', $Worker ); ?></td>
								<td><?php echo C::_( 'NIGHTHOUR', $Worker ); ?></td>
								<td><?php echo C::_( 'HOLIDAYHOUR', $Worker ); ?></td>
								<td><?php echo C::_( 'OTHERHOUR', $Worker ); ?></td>
								<td>
									<?php echo XTranslate::_( C::_( 'CHIEFNAME', $Worker ) ); ?>
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
			</div>
			<?php
		}
		else
		{
			?>
			<div class="Georgian3 text-center text-danger">
				<?php echo Text::_( 'Please, Enter Bill ID!' ); ?>
			</div>
			<?php
		}
		?>
		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
		<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
		<input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
		<input type="hidden" value="" name="task" />
	</form>
	<div class="cls"></div>
</div>