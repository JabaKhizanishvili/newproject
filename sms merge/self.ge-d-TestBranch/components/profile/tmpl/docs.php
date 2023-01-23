<?php
$UserData = $this->workerdata;
$Show = Helper::getConfig( 'show_user_documentation' );
$Files = array_diff( explode( '|', C::_( 'FILES', $UserData ) ), [ '' ] );
if ( $Show == 1 )
{
	?>
	<div class="page-container docs_block">
		<div class="docs-page-container-title">
			<span class="docs-image">
				<i class="bi bi-folder2-open"></i>
			</span>
			<?php echo Text::_( 'Documentations' ); ?>
		</div>
		<div class="docs-block">
			<?php
			if ( is_array( $Files ) && count( $Files ) )
			{
				foreach ( $Files as $File )
				{
					$fileName = substr( $File, 33 );
					$href = 'download/?f=' . $File . '&t=' . microtime( true );
					?>
					<div class="bday-item">
						<a href="<?php echo $href; ?>" target="_blank" class="upload_title">
							<?php echo $fileName; ?>
						</a>
						<div class="cls"></div>
					</div>
					<?php
				}
			}
			else
			{
				?>
				<div class="bday-item">
					<div class="no_news">
						<?php echo Text::_( 'Docs not detected' ); ?>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}