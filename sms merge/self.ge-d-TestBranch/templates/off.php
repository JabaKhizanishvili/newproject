<?php
$uri = URI::getInstance();
$site_logo = '';
$header_block = '';
$State = Helper::getConfig( 'denied_by_ip', false );
if ( !$State )
{
	Users::Redirect( '/?ref=NoRestrictOff' );
}
$Title = Helper::getConfig( 'denied_ip_list_title', '' );
$Description = Helper::getConfig( 'denied_ip_list_description', '' );
$Redirect = Helper::getConfig( 'redirect_denied_ip_to_other_page', false );
$RedirectURL = trim( Helper::getConfig( 'denied_ip_redirect_to_url', false ) );
$RedirectTImeOut = ( (int) Helper::getConfig( 'denied_ip_redirect_time', 0 )) * 1000;
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
					</div>
					<div class="cls"></div>
				</div>
			</div>
			<div class="page-body">
				<div id="login-block" class="">
					<?php require_once 'z__messages.php'; ?>
					<div class="container-fluid">
						<div class="row">
							<div class="col-6 col-lg-4">
								<div class="page-container page-narrow page-login">
									<div class="login_form">
										<div class="form-group">
											<div class="slf-login-title">
												<?php echo $Title; ?>
											</div>
										</div>
										<div class="form-group">
											<?php echo $Description; ?>
										</div>
										<?php
										if ( $Redirect && $RedirectURL )
										{
											?>
											<div class="form-group">
												<div class="form-group login-submit">
													<button type="submit" class="btn btn-danger " onclick="Redirect();">
														<?php echo Text::_( 'Redirect' ); ?>
													</button>
												</div>
											</div>
											<?php
											Helper::SetJS( 'setTimeout(Redirect, ' . $RedirectTImeOut . ');' );
										}
										?>
										<div class="cls"></div>
									</div>
								</div>
							</div>
							<?php require_once 'z__slider.php'; ?>
						</div>
					</div>
					<div class="cls"></div>
				</div>
				<div class="cls"></div>
			</div>
		</div>
		<?php echo $GLOBAL_CONTENT; ?>		
		<?php require_once 'z__footer.php'; ?>		
		<script type="text/javascript" src="<?php echo TemplateHelper::getJSLink(); ?>"></script>
		<?php echo Helper::GetJSFiles(); ?>
		<script type="text/javascript">
<?php echo Helper::GetNoInitJS(); ?>
                            $(document).ready(function () {
<?php echo Helper::GetJS(); ?>
                            });
                            /**
                             * Comment
                             */
                            function Redirect() {
                              document.location.href = '<?php echo $RedirectURL; ?>';
                            }
		</script>
	</body>
</html>

