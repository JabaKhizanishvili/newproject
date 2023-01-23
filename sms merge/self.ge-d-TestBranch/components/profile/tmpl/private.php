<?php
$UserData = $this->workerdata;
?>
<div class="page-container-padding">
	<div class="row text-center">
		<!--<div class="text-center">-->
		<div class="profile_private_value">
			<div class="profile_private_value_div">
				<?php
				$Photo = Collection::get( 'PHOTO', $UserData );
				if ( $Photo )
				{
					echo HTML::image( URL_UPLOAD . '/' . $Photo . '?v=' . Collection::get( 'MODIFIED', $UserData, time() ), 'Photo', array( 'class' => 'img-responsive' ) );
				}
				else
				{
					echo HTML::image( X_TEMPLATE . '/images/user.png', 'Photo', array( 'class' => 'img-responsive' ) );
				}
				?>
			</div>
		</div>
		<!--</div>-->
		<!--		<div class="col-md-7">
					<div class="profile_private_value_edit">
						<a class="btn btn-default" href="?option=<?php echo $this->_option_edit; ?>">
		<?php echo Text::_( 'Edit' ); ?>
						</a>
					</div>
					<div class="contact-info">
		<?php echo Collection::get( 'EMAIL', $UserData ); ?>
					</div>
					<div class="contact-info">
		<?php echo Collection::get( 'MOBILE_PHONE_NUMBER', $UserData ); ?>
					</div>
					<div class="cls"></div>
				</div>
				<div class="cls"></div>-->
	</div>


	<div class="profile_name">
		<?php echo XTranslate::_( Collection::get( 'FIRSTNAME', $UserData ), 'person' ) . ' ' . XTranslate::_( Collection::get( 'LASTNAME', $UserData ), 'person' ); ?>
		<div class="cls"></div>
	</div>
	<div class="profile_position">
		<?php echo Collection::get( 'POSITION', $UserData ); ?>
		<div class="cls"></div>
	</div>
	<div class="contact-info">
		<?php echo Collection::get( 'EMAIL', $UserData ); ?>
		<div class="cls"></div>
	</div>
	<div class="contact-info">
		<?php echo Collection::get( 'MOBILE_PHONE_NUMBER', $UserData ); ?>
		<div class="cls"></div>
	</div>

	<?php
	$ChiefsData = trim( C::get( 'ALL_CHIEFS', $UserData ) );
	if ( !empty( $ChiefsData ) && 0 )
	{
		$Chiefs = explode( ',', $ChiefsData );
		?>
		<div class="profile_private_row">
			<div class="profile_private_key">
				<?php echo Text::_( 'CHIEFS' ); ?> : 
			</div>
			<div class="profile_private_value">
				<?php
				foreach ( $Chiefs as $Chief )
				{
					?>
					<div class="profile_private_value_item">
						<?php echo $Chief; ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="cls"></div>
		</div>
		<?php
	}
	?>
</div>
