<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
/* @var $menu MenuConfig */
$menu = MenuConfig::getInstance();
$active = $menu->getActive();
$User = Users::getUser( Users::GetUserID() );
$B = trim( $User->BIRTHDATE );
if ( !empty( $B ) )
{
	$BDate = PDate::Get( $B )->toFormat( '%m-%d' );
	$NBDay = PDate::Get()->toFormat( '%m-%d' );
	?>
	<!--<div class="page_title">-->
	<?php
	if ( $BDate == $NBDay )
	{
//			require_once '_pbd.php';
	}
	?>
	<!--</div>-->
	<?php
}
?>
<div class="container-fluid">
	<div class="profile_block">
		<div class="row">
			<div class="col-md-4 col-lg-3">
				<?php
				$AllowClick = XGPS::UserCanButtonAccess();
				if ( (C::_( 'COUNTING_TYPE', $User ) == 1 || C::_( 'COUNTING_TYPE', $User ) == 2) || Helper::getConfig( 'show_server_time' ) )
				{
					?>
					<div class="page-container maindate">
						<?php
						if ( Helper::getConfig( 'show_server_time' ) )
						{
							require 'clock.php';
						}
						if ( C::_( 'COUNTING_TYPE', $User ) == 1 && $AllowClick )
						{
							require_once __DIR__ . DS . 'control.php';
						}
						if ( C::_( 'COUNTING_TYPE', $User ) == 1 && !$AllowClick )
						{
							require_once __DIR__ . DS . 'nocontrol.php';
						}
						elseif ( C::_( 'COUNTING_TYPE', $User ) == 2 )
						{
							require_once __DIR__ . DS . 'gps.php';
						}
						?>
					</div>
					<?php
				}
				?>
				<div class="page-container user_block">
					<?php require 'private.php'; ?>
				</div>
			</div>
			<div class="col-md-4 col-lg-6">
				<?php
				if ( Helper::getConfig( 'show_user_holidays' ) )
				{
					require 'limits.php';
				}
				require_once __DIR__ . DS . 'news.php';
				?>
			</div>
			<div class="col-md-4 col-lg-3">
				<?PHP
				require_once __DIR__ . DS . 'mytasks.php';
				require_once __DIR__ . DS . 'bdays.php';
				require_once __DIR__ . DS . 'docs.php';
//				require_once __DIR__ . DS . 'ptime.php';
				?>
			</div>
		</div>
	</div>
</div>
