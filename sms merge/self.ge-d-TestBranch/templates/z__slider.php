<?php
$Files = Folder::files( X_PATH_BASE . DS . 'slider', '\.png$' );
$URLBase = URI::root( true ) . 'slider/';
$Count = count( $Files );
?>
<div class="col-6 col-lg-8">
	<div id="SelfSliderIndicators" class="carousel slide" data-ride="carousel" data-interval="3000">
		<ol class="carousel-indicators">
			<?php
			$Active = 'class="active"';
			for ( $A = 0; $A < $Count; $A++ )
			{
				?>
				<li data-target="#SelfSliderIndicators" data-slide-to="<?php echo $A; ?>" <?php echo $Active; ?> ></li>
				<?php
				$Active = '';
			}
			?>
		</ol>
		<div class="carousel-inner">
			<?php
			$ActiveSlide = 'active';
			foreach ( $Files as $File )
			{
				?>
				<div class="item <?php echo $ActiveSlide; ?>">
					<img src="<?php echo $URLBase . $File; ?>" class="img-responsive" alt="">
					<?php
					$Name = File::stripExt( $File );
					$Desc = X_PATH_BASE . DS . 'slider' . DS . $Name . '.php';
					if ( is_file( $Desc ) )
					{
						require $Desc;
					}
					?>
				</div>
				<?php
				$ActiveSlide = '';
			}
			?>
		</div>
	</div>
</div>