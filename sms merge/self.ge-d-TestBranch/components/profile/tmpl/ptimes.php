<?php
if ( !empty( $on_off ) && !empty( $main_on_off ) && !empty( $limit ) && isset( $OrgsON[$OrgID] ) )
{
	$RestMinutes = Helper::getRemPrivateTime( $orgpid, 1 );
	$UsedTime = $limit - $RestMinutes;
	?>
	<div class="col-sm-6 col-md-12 col-lg-6">
		<div class="page-container">
			<div class="page-container holiday-block private_time_block">
				<div class="holiday-label">
					<?php echo Text::_( 'private time' ); ?>
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<div class="holiday-rem">
							<?php echo $RestMinutes . '<br /> ' . Text::_( 'minute' ); ?>
						</div>
						<div class="holiday-rem-label">
							<?php echo Text::_( 'Remained' ); ?>
						</div>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<div class="holiday-used">
							<?php echo $UsedTime . '<br /> ' . Text::_( 'minute' ); ?>
						</div>
						<div class="holiday-used-label">
							<?php echo Text::_( 'Used' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}