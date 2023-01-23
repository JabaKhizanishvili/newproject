<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
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
				<div class="FloatingScrollbar">
					<table class="table table-bordered table-responsive table-condensed" id="exportable">
						<tr class="bulletin_report_item_head">
							<th rowspan="2">
								<?php echo Text::_( 'WORKER' ); ?>
							</th>
							<th rowspan="2">
								<?php echo Text::_( 'PRIVATE_NUMBER' ); ?>
							</th>
							<th rowspan="2">
								<?php echo Text::_( 'ORG' ); ?>
							</th>
							<th rowspan="2">
								<?php echo Text::_( 'ORG_PLACE' ); ?>
							</th>
							<th rowspan="2">
								<?php echo Text::_( 'STAFF_SCHEDULE' ); ?>
							</th>
							<th rowspan="2">
								<?php echo Text::_( 'POSITION' ); ?>
							</th>
							<?php
							foreach ( $this->data->days as $Day )
							{
								$xDay = PDate::Get( $Day );
								?>
								<th class="text-center">
									<?php echo $xDay->toFormat( '%d %b' ); ?>
								</th>
								<?php
							}
							?>
                            <th class="text-center text-nowrap">
                                <?php echo Text::_( 'Sum' ); ?>
                            </th>
                            <th rowspan="2">
                                <?php echo Text::_( 'TOTAL MINUTES IN WORK' ); ?>
                            </th>
						</tr>
						<tr class="bulletin_report_item_head">
							<?php
							foreach ( $this->data->days as $Day )
							{
								$xDay = PDate::Get( $Day );
								?>
								<th  class="text-center">
									<?php echo $xDay->toFormat( '%a' ); ?>
								</th>
								<?php
							}
							?>
                            <td class="text-center">
                            </td>
						</tr>
						<?php
						foreach ( $this->data->Workers as $ID => $Worker )
						{
							$CountSum = 0;
							?>
							<tr>
								<td class="text-left text-nowrap">
									<?php
									$WORKER = XTranslate::_( C::_( 'WORKER', $Worker ) );
									echo $WORKER;
									$stop = null;
									foreach ( $this->data->items[$ID] as $dt => $info )
									{
										if ( $stop == null )
										{
											$stop = $dt;
										}
									}
									?>
								</td>
								<td class="text-left text-nowrap">
									<?php
									$P_id = C::_( 'PRIVATE_NUMBER', $Worker );
									$private_number = C::_( 'PRIVATE_NUMBER', $Worker ) ? ' \'' . C::_( 'PRIVATE_NUMBER', $Worker ) : '';
									echo $private_number;
									?>
								</td>
								<td class="text-left text-nowrap">
									<?php
									$ORG = C::_( 'ORG_NAME', $Worker, '-' );
									echo XTranslate::_( $ORG );
									?>
								</td>
								<td class="text-left text-nowrap">
									<?php
									$ORG_PLACE = C::_( 'ORG_PLACE', $Worker, '' );
									echo XTranslate::_( $ORG_PLACE );
									?>
								</td>
								<td class="text-left text-nowrap">
									<?php
									$SCHEDULE = C::_( 'STAFF_SCHEDULE', $Worker, '' );
									echo XTranslate::_( $SCHEDULE );
									?>
								</td>
								<td class="text-left text-nowrap">
									<?php
									$POSITION = C::_( 'POSITION', $Worker, '' );
									echo XTranslate::_( $POSITION );
									?>
								</td>
								<?php
                                $allMinutes = 0;
								foreach ( $this->data->days as $Day )
								{
									$Row = C::_( $ID . '.' . $Day . '', $this->data->items, 0 );
									$H = intval( C::_( 'DIFF', $Row, 0 ) );
									if ( $H > 0 )
									{
                                        $CountSum++;
                                        $allMinutes += $H;
										?>
										<td class="text-center">
											<?php echo $H; ?>
										</td>
										<?php
									}
									else
									{
										?>
										<td class="text-center">
											-
										</td>
										<?php
									}
								}
								?>
                                <td class="text-center">
                                    <?php echo $CountSum; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $allMinutes; ?>
                                </td>
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
