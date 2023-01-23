<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$FileName = Request::getVar( 'file', false );
$FilePath = X_EXPORT_DIR . DS . $FileName;
$FileURL = URI::root() . X_EXPORT_URL . '/' . $FileName;
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
</div>
<?php
if ( is_file( $FilePath ) )
{
	?>
	<div class = "contentCenter">
		<?php echo Text::_( 'Export DESC Ready' ); ?>
	</div>
	<div class="export_cont">
		<a href="<?php echo $FileURL; ?>" class="export_button" target="_blank" >
			<?php echo Text::_( 'Download' ); ?>
		</a>
	</div>
	<?php
}
else
{
	$msg = 'EXPORT_ERROR';
	Users::Redirect( '?option=' . DEFAULT_COMPONENT, $msg, 'error' );
}
