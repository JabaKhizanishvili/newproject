<?php
$Orgs = XGraph::getMyOrgpidOrgs();
$Apps = $this->model->GetApps();
?>
<div class="holidsall">
	<div class="tab-content">
		<?php
		$N = 0;
		$main_on_off = (int) Helper::getConfig( 'private_date' );
		$on_off = (int) Helper::getConfig( 'private_date_show' );
		$limit = (int) Helper::getConfig( 'private_date_limit' );
		$OrgsON = array_flip( Helper::CleanArray( explode( '|', Helper::getConfig( 'private_date_orgs' ) ) ) );
		foreach ( $Orgs as $org => $orgdata )
		{
			$OrgID = C::_( 'ORG', $orgdata );
			$hide = '';
			if ( $N == 0 )
			{
				$hide = ' active ';
			}
			?>
			<div id="<?php echo $org; ?>" class="tab-pane fade in row <?php echo $hide; ?>">
				<?php
				$orgpid = C::_( 'ORGPID', $orgdata );
				require 'holiday_limits.php';
				require 'ptimes.php';
				?>
			</div>
			<?php
			$N++;
		}
		?>
	</div>
	<div class="row holids">
		<div class="col-md-12">
			<ul class="nav nav-tabs tabs-below nav-justified">
				<?php
				$K = 0;
				foreach ( $Orgs as $org => $orgdata )
				{
					$disabled = '';
					if ( $K == 0 )
					{
						$disabled = ' active ';
					}
					?>
					<li view="<?php echo $org; ?>" class="nav-item <?php echo $disabled; ?>">
						<a  data-toggle="tab" href="#<?php echo $org; ?>" aria-expanded="true">
							<?php echo XTranslate::_( C::_( 'ORG_NAME', $orgdata ) ); ?>									
						</a>
					</li>
					<?php
					$K++;
				}
				?>
			</ul>
		</div>
	</div>
</div>
