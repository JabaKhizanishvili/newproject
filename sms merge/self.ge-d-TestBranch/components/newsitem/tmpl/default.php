<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$News = $this->news;
$OtherNews = $this->data->items;
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?> 
	<div class="toolbar">

	</div>
	<div class="cls"></div>
</div>

<div class="page_content">
	<div class="container">
		<h2 class="News-Title Georgian3">
			<?php echo C::_( 'TITLE', $News ); ?>
		</h2>
		<div class="bday_item_date text-right">
			<?php echo PDate::Get( C::_( 'V_PUBLISH_DATE', $News ) )->toFormat( '%d %B %Y' ); ?>
		</div>
		<div class="news-text">
			<?php
			$Photo = C::_( 'IMAGE', $News );
			if ( $Photo )
			{
				echo HTML::image( URL_UPLOAD . '/' . $Photo . '?v=1', 'Photo' );
			}
			for ( $A = 0; $A <= 9; $A++ )
			{
				echo C::_( 'TEXT' . $A, $News );
			}
			?>
		</div>
	</div>

	<br />
	<br />
	<br />
	<hr />
	<br />
	<br />

	<?php
	if ( count( $OtherNews ) )
	{
		?>
		<div class="profile_item_block">
			<div class="profile_title">
				<?php echo Text::_( 'Other News' ); ?>
			</div>
			<div class="profile_bdays">
				<ul class="list-group">
					<?php
					foreach ( $OtherNews as $Item )
					{
						?>
						<li class="list-group-item">
							<div class="row">
								<div class="bday_item_photo col-xs-4">
									<?php
									$Photo = C::_( 'IMAGE', $Item );
									if ( $Photo )
									{
										echo HTML::image( URL_UPLOAD . '/' . $Photo . '?v=1', 'Photo' );
									}
									else
									{
										echo HTML::image( 'templates/images/user.png', 'Photo' );
									}
									?>
								</div>
								<div class="col-xs-8">
									<div class="bday_item_date text-right">
										<?php echo PDate::Get( C::_( 'V_PUBLISH_DATE', $Item ) )->toFormat( '%d %B %Y' ); ?>
									</div>
									<div class="bday_item_name">
										<?php
										echo C::_( 'INTROTEXT', $Item );
										?>
									</div>
									<div class="bday_item_date text-right">
										<a href="?option=newsitem&id=<?php echo C::_( 'ID', $Item ); ?>">
											<?php echo Text::_( 'Read More...' ); ?>
										</a>
									</div>
								</div>
							</div>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
		<?php
	}
	?>
</div>
<?php
