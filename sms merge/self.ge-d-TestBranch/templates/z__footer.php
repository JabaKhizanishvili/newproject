<?php
$Company = Helper::getConfig( 'system_org' );
$Email = Helper::getConfig( 'system_email' );
$Address = Helper::getConfig( 'system_address' );
$Phone = Helper::getConfig( 'system_contact' );
?>
<div class="footer_wrapper" id="footer">
	<div class="footer">
		<div class="row footer-bottom container-lg">
			<div class="login_flogo col-lg-2 col-md-6 col-sm-6 col-xs-12">
				<a class="" href="https://hrms.self.ge?Ref=Clnt" target="_blank">
					<img src="<?php echo URI::root( 1 ); ?>templates/images/main_logo.png" alt="" />
				</a>
			</div>
			<div class="flcopy col-lg-3 col-md-6 col-sm-6 col-xs-12">
				<div class="fcopyright">
					<?php echo XTranslate::_( 'ყველა უფლება დაცულია', 'langfile' ); ?>	©
					<?php echo PDate::Get()->toFormat( '%Y' ); ?>
				</div>
			</div>
			<div class="company-info col-lg-7  col-md-12  col-sm-12 col-xs-12 row">
				<?php
				if ( $Company )
				{
					?>
					<span class="col-md-2 col-xs-12">
						<i class="bi bi-briefcase-fill"></i> <?php echo $Company; ?>
					</span>
					<?php
				}
				if ( $Address )
				{
					?>
					<span class="col-md-5 col-xs-12">
						<i class="bi bi-geo-alt-fill"></i> <?php echo $Address; ?>
					</span>
					<?php
				}
				if ( $Phone )
				{
					?>
					<span class="col-md-2 col-xs-12">
						<i class="bi bi-telephone-fill"></i> <?php echo $Phone; ?>
					</span>
					<?php
				}
				if ( $Email )
				{
					?>
					<span class="col-md-2 col-xs-12">
						<i class="bi bi-envelope-fill"></i> 
						<a href="mailto:<?php echo $Email; ?>" target="_top">
							<?php echo $Email; ?>
						</a>
					</span>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
