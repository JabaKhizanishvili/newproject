<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
?>
<div class="page_title_add">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
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
				<div class="" style="width: 100%;overflow: auto;">
					<table class="table table-bordered table-responsive table-condensed" id="exportable">
						<tr>
							<th rowspan="2">

							</th>
							<?php
							foreach ( $this->data->days as $Day )
							{
								$xDay = PDate::Get( $Day );
								?>
								<th colspan="2" class="text-center">
									<?php echo $xDay->toFormat( '%m-%d' ); ?>
								</th>
								<?php
							}
							?>
							<th rowspan="2" colspan="2"  class="text-center text-nowrap">
								<?php echo Text::_( 'Sum' ); ?>
							</th>
						</tr>
						<tr>
							<?php
							foreach ( $this->data->days as $Day )
							{
								$xDay = PDate::Get( $Day );
								?>
								<th  class="text-center" colspan="2">
									<?php echo $xDay->toFormat( '%a' ); ?>
								</th>
								<?php
							}
							?>
						</tr>
						<?php
						foreach ( $this->data->Workers as $ID => $Worker )
						{
							$MinSum = 0;
							$CountSum = 0;
							?>
							<tr>
								<td class="text-left text-nowrap">
									<?php echo $Worker; ?>
								</td>
								<?php
								foreach ( $this->data->days as $Day )
								{
									$Count = C::_( $ID . '.' . $Day . '.XCOUNT', $this->data->items, 0 );
									$Sum = C::_( $ID . '.' . $Day . '.XSUM', $this->data->items, 0 );
									$CountSum = $CountSum + (int) $Count;
									$MinSum = $MinSum + (int) $Sum;
									?>
									<td class="text-center">
										<?php echo $Count; ?>
									</td>
									<td class="text-center">
										<?php echo $Sum; ?>
									</td>
									<?php
								}
								?>
								<td>
									<?php echo $CountSum; ?>
								</td>
								<td>
									<?php echo $MinSum; ?>
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
