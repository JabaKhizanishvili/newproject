<?php
foreach ( $Data as $User )
{
	$Class = 'bday_item_type' . $Type;
	?>
	<div class="bday-item <?php echo $Class; ?>">
		<div class="row">
			<div class="bday_item_photo col-xs-4">
				<?php
				$Photo = C::_( 'PHOTO', $User );
				if ( $Photo )
				{
					echo HTML::image( URL_UPLOAD . '/' . $Photo . '?v=' . C::_( 'MODIFIED', $UserData, time() ), 'Photo' );
				}
				else
				{
					echo HTML::image( X_TEMPLATE . '/images/user.png', 'Photo', array( 'class' => 'img-responsive' ) );
				}
				?>
			</div>
			<div class="col-xs-8">
				<div class="bday_item_name">
					<?php echo XTranslate::_( C::_( 'FIRSTNAME', $User ), 'person' ); ?>
					<?php echo XTranslate::_( C::_( 'LASTNAME', $User ), 'person' ); ?>
				</div>
				<div class="bday_item_position">
					<?php echo XTranslate::_( C::_( 'POSITION', $User ) ); ?>
				</div>
				<div class="bday-type">
					<?php echo $Day; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
