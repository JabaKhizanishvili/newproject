<?php
$Show = Helper::getConfig( 'show_news' );
$News = $this->model->GetNews();
//$News = [];
if($Show != '0')
{
?>

<div class="page-container">
	<div class="news-page-container-title">
		<span class="news-image">
			<i class="bi bi-newspaper"></i>
		</span>
		<?php echo Text::_( 'News' ); ?>
	</div>
	<?php
	if ( $Show && count( $News ) )
	{
		?>
		<div class="bdays-block">
			<?php
			foreach ( $News as $Item )
			{
				?>
				<div class="bday-item">
					<div class="news_item_date text-right">
						<?php echo PDate::Get( C::_( 'V_PUBLISH_DATE', $Item ) )->toFormat( '%d %B %Y' ); ?>
					</div>
					<div class="row">
						<div class="news_item_photo col-xs-4">
							<?php
							$Photo = Collection::get( 'IMAGE', $Item );
							if ( $Photo )
							{
								echo HTML::image( URL_UPLOAD . '/' . $Photo . '?v=' . time(), 'Photo', array( 'class' => 'img-responsive' ) );
							}
							else
							{
								echo HTML::image( X_TEMPLATE . '/images/user.png', 'Photo', array( 'class' => 'img-responsive' ) );
							}
							?>
						</div>
						<div class="col-xs-8">
							<div class="news_item_name">
								<a href="?option=newsitem&id=<?php echo C::_( 'ID', $Item ); ?>">
									<?php
									echo C::_( 'TITLE', $Item );
									?>
								</a>
							</div>
							<div class="news_item_text">
								<?php
								echo C::_( 'INTROTEXT', $Item );
								?>
							</div>
							<div class="news_item_date text-right">
								<a href="?option=newsitem&id=<?php echo C::_( 'ID', $Item ); ?>">
									<?php echo Text::_( 'Read More...' ); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	<?php
	}
	else
	{
		?>
		<div class="bdays-block">
			<div class="bday-item">
				<div class="no_news">
	<?php echo Text::_( 'News not detected' ); ?>
				</div>
			</div>
		</div>
		<?php } ?>
	<div class="news-page-container-footer">
		&nbsp;
<?php // echo Text::_( 'Read More' );    ?>
	</div>
</div>
<?php }
