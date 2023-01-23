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
						Helper::getJSToolbar( 'Print', 'window.print', array() );
						?>
					</div>
					<div class="cls"></div>
				</div>
				<div class="bulletin_report_item">
					<table class="table table-bordered text-center">
						<tbody>
							<?php
							foreach ( $this->data->items as $Items )
							{
								$Worker = XTranslate::_( C::_( '0.WORKER', $Items ) );
								$ORG = !empty( C::_( '0.ORG_NAME', $Items ) ) ? ' - ' . XTranslate::_( C::_( '0.ORG_NAME', $Items ) ) : '';
								$SCHEDULE = !empty( C::_( '0.STAFF_SCHEDULE', $Items ) ) ? ' - ' . XTranslate::_( C::_( '0.STAFF_SCHEDULE', $Items ) ) : '';
								?>
								<tr class="bulletin_report_item_head">
									<td colspan="10" class="text-left Georgian2">
										<?php echo $Worker . $ORG . $SCHEDULE; ?>
									</td>
								</tr>
								<tr class="bulletin_report_item_head">
									<td class="Georgian1"><?php echo Text::_( 'Action' ); ?></td>
									<td class="Georgian1"><?php echo Text::_( 'Time' ); ?></td>
									<td class="Georgian1"><?php echo Text::_( 'DELAY_MINUTES' ); ?></td>
									<td class="Georgian1"><?php echo Text::_( 'User Comment/Reason' ); ?></td>
									<td class="Georgian1"><?php echo Text::_( 'Chief Resolution' ); ?></td>
								</tr>
								<?php
								if ( count( $Items ) )
								{
									$RecDate = null;
									foreach ( $Items as $Item )
									{
										$RecDateDate = new PDate( C::_( 'EVENT_DATE', $Item ) );
										if ( $RecDateDate->toFormat( '%d-%m-%Y' ) != $RecDate )
										{
											$RecDate = $RecDateDate->toFormat( '%d-%m-%Y' );
											?>
											<tr>
												<td colspan="10" class="text-left bp">
													<strong>
														<?php echo $RecDateDate->toFormat( '%d-%m-%Y' ) . ', ' . Text::_( $RecDateDate->toFormat( '%A' ) ); ?>						
													</strong>
												</td>
											</tr>
											<?php
										}
										$TIME_MIN = (C::_( 'TIME_MIN', $Item ) > 0) ? C::_( 'TIME_MIN', $Item ) : '';
										$TIME_COMMENT = XTranslate::_( C::_( 'TIME_COMMENT', $Item, '' ) );
										$U_COMMENT = C::_( 'U_COMMENT', $Item );
										$C_RESOLUTION = C::_( 'C_RESOLUTION', $Item );
										?>
										<tr>
											<td class="text-right"><?php echo XTranslate::_( C::_( 'EVENT_NAME', $Item ) ); ?></td>
											<td><?php echo $RecDateDate->toFormat( '%H:%M:%S' ); ?></td>
											<td><?php echo $TIME_MIN; ?></td>
											<td><?php
												if ( $TIME_MIN )
												{
													echo (empty( $U_COMMENT )) ? $TIME_COMMENT : $U_COMMENT;
												}
												else
												{
													echo $TIME_COMMENT;
												}
												?>
											</td>
											<td><?php
												if ( C::_( 'C_RESOLUTION', $Item, 0 ) == 1 )
												{
													echo Text::_( 'ADEQUATE' );
												}
												else if ( C::_( 'C_RESOLUTION', $Item, 0 ) == 2 )
												{
													echo Text::_( 'INADEQUATE' ) . ' ( ' . C::_( 'C_COMMENT', $Item, '' ) . ' )';
												}
												?></td>
										</tr>
										<?php
									}
								}
								?>
								<tr>
									<td colspan="10">
										<br />
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
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
