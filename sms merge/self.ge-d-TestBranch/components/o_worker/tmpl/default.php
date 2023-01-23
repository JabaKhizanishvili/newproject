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
	<form action="" method="post" class="form-horizontal" name="fform" id="fform">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
		if ( count( $this->data->items ) )
		{
			?>
			<div class="report_page_result">
				<div class="page_title">
					<div class="toolbar">
						<?php
						Helper::getToolbar( 'Apply', '', 'save' );
						?>
					</div>
					<div class="cls"></div>
				</div>
				<div class="bulletin_report_item scrollBox">
					<table class="table table-bordered text-center">
						<tbody>
							<tr class="bulletin_report_item_head">
								<td class="Georgian1"><?php echo Text::_( 'Action' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'Time' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'DELAY_MINUTES' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'Lateness Reason' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'Chief Resolution' ); ?></td>
							</tr>
							<?php
							foreach ( $this->data->items as $Items )
							{
								$Department = C::_( '0.DEPARTMENT', $Items );
								$Section = C::_( '0.SECTION', $Items );
								$Worker = XTranslate::_( C::_( '0.WFIRSTNAME', $Items ), 'person' ) . ' ' . XTranslate::_( C::_( '0.WLASTNAME', $Items ), 'person' );
								$ORG = C::_( '0.ORG_NAME', $Items );
								$staff_schedule = C::_( '0.STAFF_SCHEDULE', $Items );
								?>
								<tr class="bulletin_report_item_head">
									<td colspan="10" class="text-left Georgian2">
										<?php echo XTranslate::_( $ORG ) . ' - ' . XTranslate::_( $staff_schedule ); ?>
									</td>
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
										$ID = C::_( 'ID', $Item );
										$TIME_COMMENT = XTranslate::_( C::_( 'TIME_COMMENT', $Item ) );
										$C_RESOLUTION = C::_( 'C_RESOLUTION', $Item );
										$U_COMMENT = C::_( 'U_COMMENT', $Item );
										?>
										<tr>
											<td class="text-right"><?php echo XTranslate::_( C::_( 'EVENT_NAME', $Item ) ); ?></td>
											<td><?php echo $RecDateDate->toFormat( '%H:%M:%S' ); ?></td>
											<td><?php echo $TIME_MIN; ?></td>
											<!--<td><?php echo $TIME_COMMENT; ?></td>-->
											<td>
												<?php
												if ( $C_RESOLUTION )
												{
													echo $U_COMMENT;
												}
												else
												{
													if ( $TIME_MIN )
													{
														?>
														<input type="text" value="<?php echo $U_COMMENT; ?>" name="params[<?php echo $ID; ?>][U_COMMENT]" class="form-control kbd comment-text minW200" />
														<?php
													}
													else
													{
														echo $TIME_COMMENT;
													}
												}
												?>
											</td>
											<td><?php
												if ( $C_RESOLUTION == 1 )
												{
													echo Text::_( 'ADEQUATE' );
												}
												else if ( $C_RESOLUTION == 2 )
												{
													echo Text::_( 'INADEQUATE' ) . ' ( ' . C::_( 'C_COMMENT', $Item, '' ) . ' )';
												}
												?></td>
										</tr>
										<?php
									}
								}
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="page_title_add">
				<div class="toolbar">
					<?php
					Helper::getToolbar( 'Apply', '', 'save' );
					?>
				</div>
				<div class="cls"></div>
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
