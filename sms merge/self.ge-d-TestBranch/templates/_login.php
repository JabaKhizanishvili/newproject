<?php
$uri = URI::getInstance();

$site_logo = '';
$header_block = '';
if ( Users::isLogged() )
{
	$header_block = ' l_header_block ';
	$site_logo = ' i_site_logo ';
}
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<base href="<?php echo $uri->base( true ); ?>/" />
		<link href="<?php echo TemplateHelper::getCssLink(); ?>" rel="stylesheet" type="text/css" />
		<!--[if IE]>
		<link href="templates/css/ie.css" rel="stylesheet" type="text/css" />
		<![endif]-->
		<link type="image/x-icon" href="favicon.ico" rel="shortcut icon" />
		<title><?php echo Text::_( 'Site Name' ); ?></title>
	</head>
	<body>
		<div class="top login-top">
			<div class="content_wrapper">
				<div id="mainmenu" class="menu_box_wrapper">
					<div class="site_logo <?php echo $site_logo; ?>">
						<a class="main_logo" href="?ref=Logo">
							<img src="<?php echo URL_UPLOAD . Helper::getConfig( 'system_logo' ); ?>" alt="" />
						</a>
					</div>
					<div class="header_block <?php echo $header_block; ?>">
						<?php
//						include 'lights.php';
						?>
					</div>
					<div class="cls"></div>
				</div>
			</div>
			<div class="page-body">
				<div id="login-block" class="">
					<?php
//					echo '<pre><pre>';
//					print_r( Xhelp::SCAN_user() );
//					echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
					?>
					<!--<div class="content-container container-fluid">-->
					<noscript>
						<div class="noscript">
							<?php echo Text::_( 'NO_SCRIPT' ); ?>
						</div>
					</noscript>
					<?php
					$Messages = XError::getMessages();
					if ( count( $Messages ) )
					{
						foreach ( $Messages as $Message )
						{
							?>
							<div class="container-fluid">
								<div class="message noscript page-container">
									<i class="bi bi-check"></i>
									<?php echo Text::_( $Message ); ?>
								</div>
							</div>
							<?php
						}
					}
					$Errors = XError::getErrors();
					if ( count( $Errors ) )
					{
						foreach ( $Errors as $Error )
						{
							?>
							<div class="error_message noscript page-container">
								<i class="bi bi-x-lg"></i>
								<?php echo Text::_( $Error ); ?>
							</div>
							<?php
						}
					}
					$Infos = XError::getInfos();
					if ( count( $Infos ) )
					{
						foreach ( $Infos as $Info )
						{
							?>
							<div class="info_message page-container">
								<i class="bi bi-reply-fill"></i>
								<?php echo Text::_( $Info ); ?>
							</div>
							<?php
						}
					}
					$Option = Request::getVar( 'option' );
					if ( empty( $Option ) )
					{
						$Option = 'profile';
					}
					if ( $Option != 'profile' )
					{
						?>
						<div class="page-container">
							<?php
							echo $GLOBAL_CONTENT;
							?>
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
					<!--</div>-->
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
				<div class="row footer-bottom container-lg">
					<!--<div class="col-sm-5">-->
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
					<!--</div>-->
					<!--<div class="col-lg-7 col-md-2 container">-->
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
					<!--					<div class="footer-soc">
					
										</div>-->
					<!--</div>-->
				</div>
			</div>
		</div>

		<script type="text/javascript" src="<?php echo TemplateHelper::getJSLink(); ?>"></script>
		<?php echo Helper::GetJSFiles(); ?>
		<script type="text/javascript">
<?php echo Helper::GetNoInitJS(); ?>
      $(document).ready(function () {
<?php echo Helper::GetJS(); ?>
      });
		</script>
	</body>
</html>

