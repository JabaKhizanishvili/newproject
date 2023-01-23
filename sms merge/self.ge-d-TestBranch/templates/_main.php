<?php
$uri = URI::getInstance();
$FullName = Users::GetUserFullName();
$site_logo = '';
$header_block = '';
if ( Users::isLogged() )
{
	$header_block = ' l_header_block ';
	$site_logo = ' i_site_logo ';
}

$SB_header = '';
$SB_page = '';
$SB_burger = '';
$SB_main = ' SB_main ';
if ( C::_( 'side_menu', $_COOKIE, 0 ) == 1 )
{
	$SB_header = ' SB_header  ';
	$SB_page = ' SB_page ';
	$SB_burger = ' SB_burger ';
	$SB_main = ' SB_main_on ';
//	Helper::SetJS( 'side_menu(1);' );
}
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<base href="<?php echo $uri->base( true ); ?>/" />
		<link href="<?php echo TemplateHelper::getCssLink(); ?>" rel="stylesheet" type="text/css" async />
		<!--[if IE]>
		<link href="templates/css/ie.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<link type="image/x-icon" href="favicon.ico" rel="shortcut icon" />
		<title><?php echo Text::_( 'Site Name' ); ?></title>
	</head>
	<body>
		<div class="top">
			<div class="content_wrapper">
				<div id="mainmenu" class="menu_box_wrapper">
					<div class="site_logo <?php echo $site_logo; ?>">
						<a class="main_logo" href="?ref=Logo">
							<img src="<?php echo URL_UPLOAD . Helper::getConfig( 'system_logo' ); ?>" alt="" />
						</a>
					</div>
					<div class="header_block <?php
					echo $header_block;
					echo $SB_header;
					?>">
								 <?php
//							 include 'lights.php';
								 if ( Users::isLogged() )
								 {
									 ?>
							<div role="navigation" class="navbar navbar-default">
								<div class="navbar-header">
									<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
									</button>
								</div>
								<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
									<ul class="main_menu" id="main_menu">
										<?php Helper::getHeaderMenu(); ?>
									</ul>
								</div>
								<div class="cls"></div>
							</div>
							<div id="info_logout">
								<div class="info_logout">
									<?php
									$UserData = Users::getUser();
									$Photo = C::_( 'PHOTO', $UserData );
									if ( $Photo )
									{
										?>
										<span class="user_photo">
											<?php
											echo '<img src="' . URL_UPLOAD . '/' . $Photo . '?v=' . C::_( 'MODIFIED', $UserData, time() ) . '" alt="' . $FullName . '" title="' . $FullName . '" />';
											?>
										</span>
										<?php
									}
									else
									{
										?>
										<span class="user_photo">
											<i class="bi bi-person-fill"></i>
										</span>
										<?php
									}
									?>
	<!--									<span class="user_drop">
	<i class="bi bi-chevron-down"></i>
	</span>-->
									<ul class="">
										<li class="user_info">
											<?php
											echo XTranslate::_( $FullName, 'content' );
											$AdminData = Session::get( '_user_bkp' );
											if ( C::_( 'ID', $AdminData ) )
											{
												echo '&nbsp;<a href="?option=restoreloginas" class="user_restore">'
												. '<img title="' . Text::_( 'RESTOREADMIN' ) . '" alt="' . Text::_( 'RESTOREADMIN' ) . '" src="templates/images/publish_r.png">'
												. '</a>';
											}
											?>
											<li class="user_sessions">
												<a href="?option=change_password"><?php echo Text::_( 'Change Password' ); ?></a>
											</li>
										</li>
										<li class="user_sessions">
											<a href="?option=profile&layout=sessions"><?php echo Text::_( 'active sessions' ); ?></a>
										</li>
										<li class="logout">
											<a href="?option=logout">
												<?php echo Text::_( 'LogOut' ); ?> <i class="bi bi-box-arrow-right"></i>
											</a>
										</li>
									</ul>
								</div>

								<div class="cls"></div>
							</div>
							<?php
						}
						?>
					</div>
					<div class="cls"></div>
				</div>
			</div>
			<div class="page-body">
				<?php
				$PageClass = '';
				if ( Users::isLogged() )
				{
					?>
					<div id="menu-block" class="menu-expanded <?php echo $SB_page; ?>">
						<!--<div class="menu-block-white">-->
						<div class="menu-block">
							<div class="menu_block_in">
								<a class="i_img1_d" href="?ref=Logo">
									<div class="menu_block_img">
										<img class="i_img1" src="#" alt="logo"/>
									</div>
								</a>
								<div class="close-menu <?php echo $SB_burger; ?>">
	<!--								<span class="user_drop2">
										<i class="bi bi-chevron-left"></i>
										<i class="bi bi-chevron-right"></i>
									</span>-->
									<div class="cls"></div>
									<div class="user_drop2 nav-icon-3">
										<span></span>
										<span></span>
										<span></span>
									</div>
									<div class="user_drop2 nav-icon-4">
										<span></span>
										<span></span>
										<span></span>
									</div>
								</div>
								<div role="navigation" class="navbar navbar-default navbar-expanded">
									<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
										<ul class="main_menu_left" id="main_menu_left">
											<?php Helper::getHeaderMenu(); ?>
										</ul>
									</div>
									<div class="clc"></div>
								</div>
								<!--</div>-->
							</div>
						</div>
					</div>
					<?php
					$PageClass = 'page-with-menu ' . $SB_main;
				}
				?>
				<div id="page-block" class="<?php echo $PageClass; ?>">
					<div class="content-container container-fluid">
						<?php
						require_once 'z__messages.php';
						$Option = Request::getVar( 'option' );
						if ( empty( $Option ) )
						{
							$Option = 'profile';
						}
						if ( $Option != 'profile' )
						{
							?>
							<div class="page-container">
								<?php echo $GLOBAL_CONTENT; ?>
								<div class="cls"></div>
							</div>
							<?php
						}
						else
						{
							echo $GLOBAL_CONTENT;
						}
						?>
						<div class="cls"></div>
					</div>
				</div>
				<div class="cls"></div>
			</div>
		</div>
		<?php
		$Company = Helper::getConfig( 'system_org' );
		$Email = Helper::getConfig( 'system_email' );
		$Address = Helper::getConfig( 'system_address' );
		$Phone = Helper::getConfig( 'system_contact' );
		?>
		<div class="footer_wrapper" id="footer">
			<div class="footer">
				<?php
				if ( Users::isLogged() )
				{
					?>
					<div class="footer-top">
						<div class="flogo col-md-2">
							<a class="" href="https://hrms.self.ge?Ref=Clnt" target="_blank">
								<img src="<?php echo URI::root( 1 ); ?>templates/images/main_logo.png" alt="" />
							</a>
						</div>
						<div class="col-md-9">

						</div>
						<div class="col-md-1 footer-home">
							<a class="main_logo" href="?ref=Logo">
								<i class="bi bi-house-door-fill"></i>
							</a>
						</div>
					</div>
				<?php } ?>
				<div class="footer-bottom container-lg">
					<?php
					if ( !Users::isLogged() )
					{
						?>
						<div class="flogo col-lg-2">
							<a class="" href="https://www.self.ge?Ref=Clnt" target="_blank">
								<img src="<?php echo URI::root( 1 ); ?>templates/images/main_logo.png" alt="" />
							</a>
						</div>
					<?php } ?>
					<div class="col-sm-10 container">
						<div class="flcopy col-sm-3">
							<div class="fcopyright">
								<?php echo XTranslate::_( 'ყველა უფლება დაცულია', 'langfile' ); ?>	©
								<?php echo PDate::Get()->toFormat( '%Y' ); ?>
							</div>
						</div>
						<div class="company-info col-sm-9 container">
							<?php
							if ( $Company )
							{
								?>
								<span class="col-2">
									<i class="bi bi-briefcase-fill"></i> <?php echo XTranslate::_( $Company ); ?>
								</span>
								<?php
							}
							if ( $Address )
							{
								?>
								<span class="col-4">
									<i class="bi bi-geo-alt-fill"></i> <?php echo XTranslate::_( $Address ); ?>
								</span>
								<?php
							}
							if ( $Phone )
							{
								?>
								<span class="col-3">
									<i class="bi bi-telephone-fill"></i> <?php echo $Phone; ?>
								</span>
								<?php
							}
							if ( $Email )
							{
								?>
								<span class="col-3">
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
		</div>

		<script type="text/javascript" src="<?php echo TemplateHelper::getJSLink(); ?>?v=<?php echo substr( time(), 0, -2 ); ?>"></script>
		<?php echo Helper::GetJSFiles(); ?>
		<script type="text/javascript">
<?php echo Helper::GetNoInitJS(); ?>
      $(document).ready(function () {
<?php echo Helper::GetJS(); ?>
      });
      //<?php echo time(); ?>
		</script>
	</body>
</html>
