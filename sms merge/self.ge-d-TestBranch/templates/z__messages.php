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