<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
//		Helper::getJSToolbar( 'Print', 'window.print', array() );
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
								<div class="text-center">
									<?php echo Text::_( '#' ); ?>
								</div>
							</th>
							<th rowspan="2">
								<?php echo Text::_( 'worker' ); ?>
							</th>
							<th rowspan="2">
								<?php echo Text::_( 'private_number' ); ?>
							</th>
							<th rowspan="2">
								<?php echo Text::_( 'org' ); ?>
							</th>
							<th rowspan="2">
								<?php echo Text::_( 'org_place' ); ?>
							</th>
							<th rowspan="2">
								<?php echo Text::_( 'staff_schedule' ); ?>
							</th>
							<th rowspan="2">
								<?php echo Text::_( 'position' ); ?>
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
								<?php echo Text::_( 'alllateminutes' ); ?>
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
						$N = 1;
						foreach ( $this->data->Workers as $ID => $Worker )
						{
							$CountSum = 0;
							?>
							<tr>
								<td class="text-left text-nowrap text-center" style="width: 50px;">
									<?php
									echo $N;
									$N++;
									?>
								</td>
								<td class="text-left text-nowrap">
									<?php echo XTranslate::_( C::_( 'WORKER', $Worker ) ); ?>
								</td>
								<td id="tst" class="text-left text-nowrap">
									<?php
                                    echo "<span style='visibility: hidden'>'</span>".XTranslate::_( C::_( 'PRIVATE_NUMBER', $Worker ) );
//                                    echo '01'.XTranslate::_( C::_( 'PRIVATE_NUMBER', $Worker ) );
                                    ?>
								</td>
								<td class="text-left text-nowrap">
									<?php echo XTranslate::_( C::_( 'ORG_NAME', $Worker ) ); ?>
								</td>
								<td class="text-left text-nowrap">
									<?php echo XTranslate::_( C::_( 'ORG_PLACE', $Worker ) ); ?>
								</td>
								<td class="text-left text-nowrap">
									<?php echo XTranslate::_( C::_( 'STAFF_SCHEDULE', $Worker ) ); ?>
								</td>
								<td class="text-left text-nowrap">
									<?php echo XTranslate::_( C::_( 'POSITION', $Worker ) ); ?>
								</td>
								<?php
								$allMinutes = 0;
								foreach ( $this->data->days as $Day )
								{
									$Row = C::_( $ID . '.' . $Day . '', $this->data->items, 0 );
									$C_RESOLUTION = C::_( 'C_RESOLUTION', $Row, null );
									$Sum = $C_RESOLUTION == 1 ? 0 : C::_( 'TIME_MIN', $Row, null );
									if ( $Sum )
									{
										$CountSum++;
										$allMinutes += $Sum;
									}
									?>
									<td class="text-center">
										<?php echo $Sum; ?>
									</td>
									<?php
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
