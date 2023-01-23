<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );

$config0 = Helper::getConfig( 'r_tabel_htable_identificator' );
$show_private_number = Helper::getConfig( 'show_private_number' );
$with_rest = Helper::getConfig( 'worked_hours' );
$salary_col = Helper::getConfig( 'salary_col' );
$work_days_col = Helper::getConfig( 'work_days_col' );
$real_days_col = Helper::getConfig( 'real_days_col' );
$missed_days_col = Helper::getConfig( 'missed_days_col' );
$graph_full_time_col = Helper::getConfig( 'graph_full_time_col' );
$full_worked_col = Helper::getConfig( 'full_worked_col' );
$stand_whours_col = Helper::getConfig( 'stand_whours_col' );
$mission_col = Helper::getConfig( 'mission_col' );
$hol_mission_col = Helper::getConfig( 'hol_mission_col' );
$paid_missed_col = Helper::getConfig( 'paid_missed_col' );
$not_paid_missed_col = Helper::getConfig( 'not_paid_missed_col' );
$mission_sum_col = Helper::getConfig( 'mission_sum_col' );

$normalized_time = Helper::getConfig( 'normalized_time' );
$week_days = explode( '|', Helper::getConfig( 'week_days' ) );
$work_duration = Helper::getConfig( 'work_duration' );
$time_code_show = Helper::getConfig( 'time_code_show' );
$colspan = $time_code_show == 1 ? 4 : 3;

$show_schedule_code = Helper::getConfig( 'show_schedule_code' );
$show_unit_code = Helper::getConfig( 'show_unit_code' );
$show_worker_code = Helper::getConfig( 'show_worker_code' );

$AllHolidays = Helper::GetAllHoldays();
$XTable = new XHRSTable();
$HWItems = HolidayLimitsTable::GetHolidayIDx( 0, 'a' );
$HWLItems = HolidayLimitsTable::GetHolidayIDx( 1, 'a' );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
//						Helper::getJSToolbar( 'Print', 'window.print', array() );
		Helper::getJSToolbar( 'Export To Exel', 'exportTableToExcel', array( '#exportable' ) );
		?>
	</div>
	<div class="cls"></div>
</div>

<div class="page_content">
	<form action="" method="get" name="fform" id="fform" class="form-horizontal">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
		if ( count( $this->data->items ) )
		{
			?>
			<div class="report_page_result">
				<div class="double-scroll FloatingScrollbar">
					<table class="table table-bordered table-condensed" id="exportable">
						<tr>
							<th rowspan="2" class="text-center">
								<?php echo Text::_( '#' ); ?>
							</th>
							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'Worker' ); ?>
							</th>
							<?php
							if ( $show_worker_code == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'Worker_code' ); ?>
								</th>
							<?php } ?>
	<!--							<th rowspan="2" class="text-center">
							<?php echo Text::_( 'PRIVATE_NUMBER' ); ?>
	</th>-->
							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'ORG' ); ?>
							</th>
	<!--							<th rowspan="2" class="text-center">
							<?php echo Text::_( 'ORG_PLACE' ); ?>
							</th>-->
							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'STAFF_SCHEDULE' ); ?>
							</th>
							<?php
							if ( $show_schedule_code == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'SCHEDULE_CODE' ); ?>
								</th>
								<?php
							}
							if ( $config0 == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'TABLENUM' ); ?>
								</th>
								<?php
							}
							if ( $show_private_number == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'PRIVATE_NUMBER' ); ?>
								</th>
							<?php } ?>

							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'Position' ); ?>
							</th>
							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'ORG_PLACE' ); ?>
							</th>

							<?php
							if ( $show_unit_code == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'UNIT_CODE' ); ?>
								</th>
								<?php
							}
							if ( $salary_col == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'SALARY' ); ?>
								</th> <?php } ?>

							<?php
							if ( $work_days_col == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'Graph Worked Days' ); ?>
								</th><?php } ?>

							<!--here-->
							<?php
							if ( $real_days_col == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'REAL WORK DAY' ); ?>
								</th><?php } ?>
							<?php
							if ( $missed_days_col == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'MISSED DAY' ); ?>
								</th>
							<?php } ?>
							<!--here-->

							<?php
							if ( $graph_full_time_col == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'GRAPH FULL TIME' ); ?>
								</th>
								<?php
							} if ( $full_worked_col == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'FULL WORKED TIME' ); ?>
								</th>
								<?php
							} if ( $stand_whours_col == 1 )
							{
								?>

								<!--here-->
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'STANDARD WORKED HOURS' ); ?>
								</th>
							<?php } ?>
							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'LATENES HOURS' ); ?>
							</th>
							<!--here-->

							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'Overtime' ); ?>
							</th>
							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'WAGED HOLIDAY' ); ?>
							</th>
							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'WageLess Holiday' ); ?>
							</th>
							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'Bulletin' ); ?>
							</th>
							<?php
							if ( $mission_col == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'Mission' ); ?>
								</th><?php
							} if ( $hol_mission_col == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'Holiday Mission' ); ?>
								</th><?php
							}
							if ( $mission_sum_col == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'Mission Sum' ); ?>
								</th>
							<?php } ?>

							<!--here-->
							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'NIGHT WORKED HOURS' ); ?>
							</th>
							<th rowspan="2" class="text-center">
								<?php echo Text::_( 'HOLIDAY WORKED HOURS' ); ?>
							</th>
							<?php
							if ( $paid_missed_col == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'PAID MISSED HOURS' ); ?>
								</th><?php
							} if ( $not_paid_missed_col == 1 )
							{
								?>
								<th rowspan="2" class="text-center">
									<?php echo Text::_( 'NOT PAID MISSED HOURS' ); ?>
								</th><?php } ?>
							<!--here-->

							<?php
							foreach ( $this->data->days as $Day )
							{
								$xDay = PDate::Get( $Day );
								?>
								<th class="text-center" colspan="<?php echo $colspan; ?>">
									<?php echo $xDay->toFormat( '%d %b, %a' ); ?>
								</th>
								<?php
							}
							?>
						</tr>
						<tr>
							<?php
							foreach ( $this->data->days as $Day )
							{
								$xDay = PDate::Get( $Day );
								?>
								<th  class="text-center">
									<?php echo Text::_( 'Graph hours.' ); ?>
								</th>
								<th  class="text-center">
									<?php echo Text::_( 'Work hours.' ); ?>
								</th>
								<th  class="text-center">
									<?php echo Text::_( 'Overtime.' ); ?>
								</th>
								<?php
								if ( $time_code_show == 1 )
								{
									?>
									<th  class="text-center">
										<?php echo Text::_( 'Time code.' ); ?>
									</th>
									<?php
								}
							}
							?>
						</tr>
						<?php
						$nn = 1;
						foreach ( $this->data->Workers as $ID => $Worker )
						{
							$CountSum = 0;
							$orgpid = C::_( 'ORGPID', $Worker );
							$Position = XTranslate::_( C::_( 'POSITION', $Worker ) );
							$OrgPlace = XTranslate::_( C::_( 'ORG_PLACE', $Worker ) );
							?>
							<tr>
								<td class="text-left text-nowrap text-center">
									<?php
									echo $nn;
									$nn++;
									?>
								</td>
								<td class="text-left text-nowrap">
									<?php echo XTranslate::_( C::_( 'FIRSTNAME', $Worker ), 'person' ) . ' ' . XTranslate::_( C::_( 'LASTNAME', $Worker ), 'person' ); ?>
								</td>
		<!--								<td class="text-left text-nowrap">
								<?php echo C::_( 'PRIVATE_NUMBER', $Worker ); ?>
								</td>-->
								<?php
								if ( $show_worker_code == 1 )
								{
									?>
									<td class="text-left text-nowrap">
										<?php echo XTranslate::_( C::_( 'P_CODE', $Worker ) ); ?>
									</td>
								<?php } ?>
								<td class="text-left text-nowrap">
									<?php echo XTranslate::_( C::_( 'ORG_NAME', $Worker ) ); ?>
								</td>
		<!--								<td class="text-left text-nowrap">
								<?php echo XTranslate::_( C::_( 'ORG_PLACE', $Worker ) ); ?>
								</td>-->
								<td class="text-left text-nowrap">
									<?php echo XTranslate::_( C::_( 'STAFF_SCHEDULE', $Worker ) ); ?>
								</td>
								<?php
								if ( $show_schedule_code == 1 )
								{
									?>
									<td class="text-left text-nowrap">
										<?php echo XTranslate::_( C::_( 'SCHEDULE_CODE', $Worker ) ); ?>
									</td>
									<?php
								}
								if ( $config0 == 1 )
								{
									?><td class="text-left text-nowrap"><?php
									echo C::_( 'TABLENUM', $Worker, '' );
									?></td><?php
								}
								if ( $show_private_number == 1 )
								{
									?><td class="text-left text-nowrap"><?php
										echo C::_( 'PRIVATE_NUMBER', $Worker ) ? '\'' . C::_( 'PRIVATE_NUMBER', $Worker ) : '';
										?></td><?php
								}
								?>
								<td class="text-left text-nowrap">

									<?php
									echo $Position;
									?> 
								</td>
								<td class="text-left text-nowrap">
									<?php
									echo $OrgPlace;
									?>
								</td>
								<?php
								if ( $show_unit_code == 1 )
								{
									?>
									<td class = "text-left text-nowrap">
										<?php
										echo C::_( 'UNIT_CODE', $Worker );
										?>
									</td>
									<?php
								}
								if ( $salary_col == 1 )
								{
									?>
									<td class="text-left text-nowrap">
										<?php echo C::_( 'SALARY', $Worker ); ?> <?php // echo Text::_('GEL');                ?>
									</td>
									<?php
								}
								$RealDay = 0;
								$RealHours = 0;
								$SumLatenes = 0;
								$FWorkeds = 0;
								$Workeds = 0;
								$Overtimes = 0;
								$WHolidays = 0;
								$WHolidaysHours = 0;
								$WLHolidays = 0;
								$WLHolidaysHours = 0;
								$Bullettins = 0;
								$BullettinsHours = 0;
								$Missions = 0;
								$HolidayMissions = 0;
								$WorkingDay = 0;
								$NightHours = 0;
								$HOLIDAY_worked_hours = 0;
								ob_start();
								foreach ( $this->data->days as $Day )
								{
									$WH = 0;
									$Row = C::_( $ID . '.' . $Day . '', $this->data->items, 0 );
									$StartTime = C::_( 'START_TIME', $Row );
									$EndTime = C::_( 'END_TIME', $Row );
									$EventDate = C::_( 'EVENT_DATE', $Row );
									$TimeID = C::_( 'TIME_ID', $Row, false );
									$time_code = C::_( 'TIME_CODE', $Row, false );
									$NIGHT = 0;
									if ( $TimeID )
									{
										$StartDate = PDate::Get( $EventDate . ' ' . $StartTime );
										$EndDate = PDate::Get( $EventDate . ' ' . $EndTime );
										if ( $StartDate->toUnix() >= $EndDate->toUnix() )
										{
											$EndDate = PDate::Get( $EventDate . ' ' . $EndTime . ' + 1day' );
										}
										$NIGHT = $XTable->NightHourCalculator( $StartDate->toFormat(), $EndDate->toFormat() );
										if ( $NIGHT > 0 )
										{
											$Latenes = $XTable->NightHourLatenesCalculator( $ID, $StartDate->toFormat(), $EndDate->toFormat() );
//										$NIGHT -= $XTable->NightHourCalculator( C::_( 'START_BREAK', $Row ), C::_( 'END_BREAK', $Row ) );
											$Rest = $XTable->NightHourRestCalculator( $ID, $TimeID, $StartDate->toFormat(), $EndDate->toFormat() );
											$NIGHT = Helper::FormatBalance( $XTable->ReturnNightTime( $NIGHT - $Latenes - $Rest ), 2 );
										}
									}
									$NightHours += $NIGHT;
//									here

									$Worked = round( C::_( 'WORKINGHOURS', $Row, 0 ), 2 );
									$Rest_Time = round( C::_( 'REST_TIME', $Row, 0 ), 2 );
									if ( $with_rest == 0 )
									{
										$Worked -= $Rest_Time;
									}
									$Latenes = round( C::_( 'LATENESS', $Row, 0 ), 2 );
//									$Overtime = C::_( 'OVERTIME', $Row, null );
									$Overtime = C::_( 'OverTimes.' . $ID . '.' . $Day, $this->data, 0 );
									$RW = $Worked - $Latenes + $Overtime;
									$TimeID = C::_( 'TIME_ID', $Row, false );
									$FWorkeds += $Worked;
//									$Workeds += $RW;
									$Overtimes += $Overtime;

									if ( array_key_exists( C::_( 'EVENT_DATE', $Row ), $AllHolidays ) )
									{
										$HOLIDAY_worked_hours += $Worked - $Latenes;
									}
									?>
									<td class="text-center">
										<?php
										echo abs( $Worked );
										?>
									</td>
									<td class="text-center">
										<?php
										if ( $TimeID )
										{
											$WorkingDay++;
											$Out = C::_( $orgpid . '.' . $Day, $this->data->Outs, false );
											if ( $Out === false )
											{
//												echo Helper::FormatBalance( $RW, 2 );
												echo $RW > 0 ? Helper::FormatBalance( $RW, 2 ) : '0,00';
												if ( $RW > 0 )
												{
													$RealDay++;
													$Workeds += $RW;
													$SumLatenes += $Latenes;
												}
											}
											else
											{
												if ( isset( $HWItems[$Out] ) )
												{
													$Out = 0;
												}
												elseif ( isset( $HWLItems[$Out] ) )
												{
													$Out = 1;
												}

												switch ( $Out )
												{
													case 0:
														echo Text::_( 'H' );
														$WHolidays++;
														$WHolidaysHours += $Worked;
														break;
													case 1:
														echo Text::_( 'WLH' );
														$WLHolidays++;
														$WLHolidaysHours += $Worked;
														break;
													case 5:
														echo Text::_( 'B' );
														$Bullettins++;
														$BullettinsHours += $Worked;
														break;
													case 7:
														echo Text::_( 'M' );
														$Missions++;
														$RealDay++;
														break;
													default:
														echo '?';
														break;
												}
											}
										}
										else
										{
											$Out = C::_( $ID . '.' . $Day, $this->data->Outs, false );
											if ( $Out == 7 )
											{
												echo Text::_( 'M' );
												$HolidayMissions++;
											}
											else
											{
												echo Helper::FormatBalance( $RW, 2 );
											}
										}
										?>
									</td>
									<td class="text-center">
										<?php
										if ( $Overtime > 0 )
										{
											echo Helper::FormatBalance( $Overtime, 2 );
										}
										?>
									</td>
									<?php
									if ( $time_code_show == 1 )
									{
										?>
										<td class="text-center">
											<?php
											echo $time_code;
											?>
										</td>
										<?php
									}
//									$Normalized = Helper::NormalizedTime( C::_( 'EVENT_DATE', $Row ),  $week_days, $work_duration);
								}
								$Cols = ob_get_clean();
								$MissedDay = $WorkingDay - $RealDay - $WHolidays - $WLHolidays - $Bullettins;
								$PaidMissed = $WHolidaysHours + $BullettinsHours;
								$UnPaidMissed = $WLHolidaysHours + $SumLatenes;
								?>
								<?php
								if ( $work_days_col == 1 )
								{
									?>
									<td class="text-center">
										<?php echo $WorkingDay; ?>
									</td><?php } ?>

								<!--here-->
								<?php
								if ( $real_days_col == 1 )
								{
									?>
									<td class="text-center text-nowrap">
										<?php echo $RealDay; ?>
									</td><?php
								} if ( $missed_days_col == 1 )
								{
									?>
									<td class="text-center text-nowrap">
										<?php
										echo $MissedDay;
										?>
									</td>
								<?php } ?>
								<!--here-->

								<?php
								if ( $graph_full_time_col == 1 )
								{
									?>
									<td class="text-center">
										<?php
										echo Helper::FormatBalance( $FWorkeds, 2 );
										?>
									</td>
									<?php
								} if ( $full_worked_col == 1 )
								{
									?>
									<td class="text-center">
										<?php echo Helper::FormatBalance( $Workeds, 2 ); ?>
									</td><?php
								} if ( $stand_whours_col == 1 )
								{
									?>

									<!--here-->
									<td class="text-center text-nowrap">
										<?php
										echo Helper::FormatBalance( $Workeds - $Overtimes, 2 );
										?>
									</td><?php } ?>
								<td class="text-center text-nowrap">
									<?php
									echo Helper::FormatBalance( $SumLatenes, 2 );
									?>
								</td>
								<!--here-->
								<td class="text-center">
									<?php echo Helper::FormatBalance( $Overtimes + $HOLIDAY_worked_hours, 2 ); ?>
								</td>
								<td class="text-center">
									<?php echo $WHolidays; ?>
								</td>
								<td class="text-center">
									<?php echo $WLHolidays; ?>
								</td>
								<td class="text-center">
									<?php echo $Bullettins; ?>
								</td>
								<?php
								if ( $mission_col == 1 )
								{
									?>
									<td class="text-center">
										<?php echo $Missions; ?>
									</td><?php
								} if ( $hol_mission_col == 1 )
								{
									?>
									<td class="text-center">
										<?php echo $HolidayMissions; ?>
									</td><?php } ?>

								<!--here-->
								<?php
								if ( $mission_sum_col == 1 )
								{
									?>
									<td class="text-center">
										<?php echo $Missions + $HolidayMissions; ?>
									</td>
								<?php } ?>
								<td class="text-center text-nowrap">
									<?php echo $NightHours; ?>
								</td>
								<td class="text-center text-nowrap">
									<?php echo Helper::FormatBalance( $HOLIDAY_worked_hours, 2 ); ?>
								</td>
								<?php
								if ( $paid_missed_col == 1 )
								{
									?>
									<td class="text-center text-nowrap">
										<?php echo Helper::FormatBalance( $PaidMissed, 2 ); ?>
									</td><?php
								} if ( $not_paid_missed_col == 1 )
								{
									?>
									<td class="text-center text-nowrap">
										<?php echo Helper::FormatBalance( $UnPaidMissed, 2 ); ?>
									</td><?php
								}

								echo $Cols;
								?>
							</tr>
							<?php
						}
						?>
					</table>
				</div>
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
</div>
<?php
$this->setHelp();
