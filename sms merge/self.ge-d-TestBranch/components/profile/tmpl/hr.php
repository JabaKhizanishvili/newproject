<?php
$main_on_off = (int) Helper::getConfig( 'private_date' );
$on_off = (int) Helper::getConfig( 'private_date_show' );
$limit = (int) Helper::getConfig( 'private_date_limit' );

$Apps = $this->model->GetApps();
$Orgs = [];
foreach ( $Apps as $app )
{
	$orgid = C::_( 'ORGID', $app );
	$Orgs[$orgid] = C::_( $orgid, $Orgs, [] );
	$Orgs[$orgid][] = $app;
}
$unique = 'holiday';
?>

<div class="holidsall">
	<div class="tab-content">
		<?php
		$N = 0;
		foreach ( $Orgs as $key => $Item )
		{
			$Key = $key . $unique;
			$hide = '';
			if ( $N == 0 )
			{
				$hide = ' active ';
			}
			?>
			<div id="<?php echo $Key; ?>" class="tab-pane fade in row<?php echo $hide; ?>">
				<?php
				foreach ( $Item as $App )
				{
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
					$N++;
				}
				if ( $main_on_off == 1 && $on_off == 1 && !empty( $limit ) )
				{
					$data = $this->model->collect_ptime( $key, $limit );
					if ( !empty( $data ) )
					{
						$RestMinutes = C::_( 'RESTMINUTES', $data );
						$UsedTime = C::_( 'USEDTIME', $data );
						?>
						<div class="col-sm-6 col-md-12 col-lg-6">
							<div class="page-container ptime-block">
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
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
	<?php
	if ( count( $Orgs ) > 1 )
	{
		?>
		<div class="row holids">
			<div class="col-md-12">
				<ul class="nav nav-tabs tabs-below nav-justified">
					<?php
					$K = 0;
					foreach ( $Orgs as $key => $Item )
					{
						$Key = $key . $unique;
						$org_name = C::_( '0.ORG_NAME', $Item );

						$disabled = '';
						if ( $K == 0 )
						{
							$disabled = ' active ';
						}
						?>
						<li view="<?php echo $Key; ?>" class="nav-item <?php echo $disabled; ?>">
							<a  data-toggle="tab" href="#<?php echo $Key; ?>" aria-expanded="true">
								<?php echo XTranslate::_( $org_name ); ?>									
							</a>
						</li>
						<?php
						$K++;
					}
					?>
				</ul>
			</div>
		</div>
		<?php
	}
	?>
</div>