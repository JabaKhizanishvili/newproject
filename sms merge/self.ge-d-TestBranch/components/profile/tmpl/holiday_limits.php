<?php
foreach ( $Apps as $App )
{
	if ( $App->ORGID != $org )
	{
		continue;
	}
	?>
	<div class="col-sm-6 col-md-12 col-lg-6">
		<div class="page-container holiday-block">
			<div class="holiday-label">
				<?php echo XTranslate::_( $App->LIB_TITLE ); ?>
			</div>
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="holiday-rem">
						<?php echo $App->COUNT . '<br /> ' . Text::_( 'Day' ); ?>
					</div>
					<div class="holiday-rem-label">
						<?php echo Text::_( 'Remained' ); ?>
					</div>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
					<div class="holiday-used">
						<?php echo $App->REST . '<br /> ' . Text::_( 'Day' ); ?>
					</div>
					<div class="holiday-used-label">
						<?php echo Text::_( 'Used' ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}