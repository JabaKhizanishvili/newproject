<?php
$uri = URI::getInstance();
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
		<div class="top">
			<div class="content_wrapper_modal">
				<?php
				$msg = trim( Request::getVar( 'msg', '' ) );
				$Messages = XError::getMessages();
				if ( $msg )
				{
					?>
					<div class="message noscript">
						<?php echo Text::_( $msg ); ?>
					</div>
					<?php
				}
				if ( count( $Messages ) )
				{
					foreach ( $Messages as $Message )
					{
						?>
						<div class="message noscript">
							<?php echo Text::_( $Message ); ?>
						</div>
						<?php
					}
				}
				$Errors = XError::getErrors();
				$error = trim( Request::getVar( 'error', '' ) );
				if ( $error )
				{
					?>
					<div class="error_message noscript">
						<?php echo Text::_( $error ); ?>
					</div>

					<?php
				}
				if ( count( $Errors ) )
				{
					foreach ( $Errors as $Error )
					{
						?>
						<div class="error_message noscript">
							<?php echo Text::_( $Error ); ?>
						</div>
						<?php
					}
				}
				?>
				<!-- Content -->
				<div class="content_main round_border shadow">
					<?php
					echo $GLOBAL_CONTENT;
					?>
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

