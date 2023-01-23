<?php
$Show = Helper::getConfig( 'show_bdays' );

if ( $Show )
{
	$bday_range = Helper::getConfig( 'bday_range' );
	$UserData = $this->model->GetBdays( $bday_range, 2 );
	if ( count( $UserData ) )
	{
		$Type = 0;
		?>
		<div class="page-container i_bday">
			<div class="bday-page-container-title">
				<span class="bday-image">
					<?php echo HTML::image( X_TEMPLATE . '/images/cake.png', '' ); ?>
				</span>
				<?php echo Text::_( 'BirthDays' ); ?>
			</div>
			<div class="bdays-block">
				<?php
				$Today = PDate::Get()->toFormat( '%m-%d' );
				$Tommorow = PDate::Get( 'now + 1 day' )->toFormat( '%m-%d' );

				foreach ( $UserData as $key => $val )
				{
					$bdclass = ' bday-green yellow ';
					if ( $key == $Today )
					{
						$bdclass = ' bday-red ';
						$key = Text::_( 'Today' );
					}
					elseif ( $key == $Tommorow )
					{
						$bdclass = ' bday-green ';
						$key = Text::_( 'Tommorow' );
					}
					else
					{
						$bd = C::_( '0.BIRTHDATE', $val );
						$key = PDate::Get( $bd )->toFormat( '%d %B' );
					}
					$Data = $val;
					$Day = '<span class="' . $bdclass . '">' . $key . '</span>';
					require '_bdayitem.php';
					$Type = 1;
				}
				?>
			</div>
		</div>
		<?php
	}
}

	
