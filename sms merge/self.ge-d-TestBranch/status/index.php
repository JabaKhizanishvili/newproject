<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );

session_start();
include( PATH_BASE . DS . 'define.php');
include( PATH_BASE . DS . 'include.php');
include( PATH_BASE . DS . 'libraries' . DS . 'cache' . DS . 'cache.php');
global $DisableSF;
$DisableSF = 1;
$IP = Request::getVar( 'REMOTE_ADDR', 0, 'server' );
Users::InitUser( false );
if ( !Users::isLogged() )
{
	$RestResponse = new RestResponse();
	$StatusCode = 203;
	$requestContentType = 'text/plain';
	$RestResponse->setHttpHeaders( $requestContentType, $StatusCode );
	die;
}
$uri = clone(URI::getInstance());
// Get the path
//Remove basepath
$Path = str_replace( 'index.php', '', substr_replace( $uri->getPath(), '', 0, strlen( URI::base( true ) ) ) );
//Set the route
$uri->setPath( trim( $Path, '/' ) );
$UserName = $uri->getPath();
$LogPath = PATH_LOGS . DS . 'LoginData';
if ( !Folder::exists( $LogPath ) )
{
	Folder::create( $LogPath, 0777 );
}
$UserID = null;

if ( !$UserID )
{
	$User = Users::getUserByField( 'ldap_username', mb_strtolower( $UserName ) );
	$UserID = (int) C::_( 'ID', $User );
//	$Cache->Store( $UserID, $UserName );
}
$CountingType = C::_( 'COUNTING_TYPE', $User );
$Confirm = 1;
$GPS = '';
$Function = 'doAction';
if ( $CountingType == 2 )
{
	$GPS = 'gps';
	$Function = 'doActionGPS';
	$Confirm = 0;
}
else
{
	$AllowClick = XGPS::UserCanButtonAccess();
	if ( !$AllowClick )
	{
		?>
		<div class="counting-system-msg">
			<?php echo Text::_( 'YOU ARE NOT IN OFFICE' ); ?>
		</div>
		<?php
		if ( $IP )
		{
			?>
			<br />
			<div class="counting-system-msg">
				<?php echo Text::_( 'YOUR IP IS' ); ?> : <?php echo $IP ?>
			</div>
			<?php
		}
		?>

		<?php
		$RestResponse = new RestResponse();
		$StatusCode = 200;
		$requestContentType = 'text/html';
		$RestResponse->setHttpHeaders( $requestContentType, $StatusCode );
		die;
	}
}
//$User = Users::getUserByField( 'ldap_username', $UserName );
//$UserID = (int) C::_( 'ID', $User );

if ( empty( $UserID ) )
{
	die( 'Error!' );
}
$UserFile = $LogPath . DS . $UserID;
if ( File::exists( $UserFile ) )
{
	$Status = file_get_contents( $UserFile );
}
else
{
	$UsersIDX = Helper::GetUserInOutStatus( $UserID );

	$Status = 2;
	foreach ( $UsersIDX as $ST )
	{
		$Status = C::_( 'STATUS_ID', $ST, 2 );
		if ( $Status == 1 )
		{
			break;
		}
	}
	file_put_contents( $UserFile, $Status );
}


if ( $Status == 1 )
{
	?>
	<span class="btn-LogOut">
		<button class="btn btn-primary" type="button" onclick="<?php echo $Function; ?>('profileedit', '<?php echo $GPS; ?>logout', 0, <?php echo $Confirm; ?>);">
			<?php echo Text::_( 'System LogOut' ); ?>
			<i class="bi bi-chevron-right"></i>
		</button>
	</span>
	<?php
}
else
{
	?>
	<span class="btn-LogIn">
		<button class="btn btn-primary" type="button" onclick="<?php echo $Function; ?>('profileedit', '<?php echo $GPS; ?>login', 0, <?php echo $Confirm; ?>);">
			<?php echo Text::_( 'System LogIn' ); ?>
			<i class="bi bi-chevron-right"></i>
		</button>
	</span>
	<?php
}
die;
