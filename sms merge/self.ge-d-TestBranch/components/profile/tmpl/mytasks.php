<?php
$Show = (int) Helper::getConfig( 'show_user_tasks' );
if ( $Show == 1 )
{
	$Files = $this->model->getMyTasks();
	?>
	<div class="page-container docs_block">
		<div class="docs-page-container-title d_pink">
			<span class="docs-image">
				<i class="bi bi-megaphone-fill" style="color:white;"></i>
			</span>
			<?php echo Text::_( 'MyTasks' ); ?>
			</div>
		<div class="docs-block l_pink">
			<?php
			if ( count( $Files ) )
			{
				?>
				<?php
				if ( is_array( $Files ) )
				{
					$href = '?option=mytasks';
					foreach ( $Files as $File )
					{
						$line = XTranslate::_( C::_( 'LIB_TITLE', $File ) ) . ' - ' . XTranslate::_( C::_( 'FIRSTNAME', $File ) ) . ' ' . XTranslate::_( C::_( 'LASTNAME', $File ) );
						?>
						<div class="bday-item">
							<?php echo $line; ?>
							<div class="cls"></div>
						</div>
					<?php }
					?>
					<a href="<?php echo $href; ?>" class="upload_title">
						<div class="bday-item text-center " style="color:white;">
							<?php echo Text::_( 'All my Tasks' ); ?>
							<div class="cls"></div>
						</div>
					</a>
					<?php
				}
			}
			else
			{
				?>
				<div class="bday-item">
					<div class="no_news">
						<?php echo Text::_( 'Tasks not detected' ); ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php
}
