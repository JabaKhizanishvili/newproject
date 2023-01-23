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
				<div class="" style="width: 100%;overflow: auto;">
					<table class="table table-bordered table-responsive table-condensed" id="exportable">
						<tr class="bulletin_report_item_head">
							<th rowspan="2">
								<?php echo Text::_( 'workers' ); ?>
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
						</tr>
						<?php
						foreach ( $this->data->Workers as $ID => $Wdata )
						{
							$Worker = XTranslate::_( C::_( 'WORKER', $Wdata ) );
							$Org_name = XTranslate::_( C::_( 'ORG_NAME', $Wdata ) );
							$Org_Place_Name = XTranslate::_( C::_( 'ORG_PLACE_NAME', $Wdata ) );
							$Staff_schedule = XTranslate::_( C::_( 'STAFF_SCHEDULE', $Wdata ) );
							$CountSum = 0;
							?>
							<tr>
                                <td class="text-left text-nowrap">
                                    <?php echo $Worker; ?>
                                </td>
                                <td class="text-left text-nowrap">
                                    <?php echo $Org_name; ?>
                                </td>
                                <td class="text-left text-nowrap">
                                    <?php echo $Org_Place_Name; ?>
                                </td>
								<td class="text-left text-nowrap">
									<?php echo $Staff_schedule; ?>
								</td>
								<?php
								foreach ( $this->data->days as $Day )
								{
									$Row = C::_( $ID . '.' . $Day . '', $this->data->items, 0 );
									if ( $Row )
									{
										$PDate = PDate::Get( C::_( 'P_EVENT_DATE', $Row ) )->toFormat( '%H:%M' );
										?>
										<td class="text-center">
											<?php echo $PDate; ?>
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
