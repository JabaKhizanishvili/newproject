<div class="counting-system">
	<div class="row">
		<div class="col-xs-5">
			<div class="counting-system-clock">
				<?php echo HTML::image( X_TEMPLATE . '/images/clock.png', '', array( 'class' => 'img-responsive' ) ); ?>
			</div>
		</div>
		<div class="col-xs-7 nopadding">
			<div class="counting-system-msg">
				<?php echo Text::_( 'FIx it' ); ?>
			</div>
			<div class="counting-system-desc">
				<?php echo Text::_( 'check-in - check-out' ); ?>
			</div>
		</div>
		<div class="cls"></div>
	</div>

	<div class="buttons-container" id="ButtonControlSet">
		<div class="counting-system-msg">
			<?php echo Text::_( 'YOU ARE NOT IN OFFICE' ); ?>
		</div>
		<?php
		$IP = Request::getVar( 'REMOTE_ADDR', 0, 'server' );
		if ( $IP )
		{
			?>
			<br />
			<div class="counting-system-msg">
				<?php echo Text::_( 'YOUR IP IS' ); ?> : <?php echo $IP ?>
			</div>
			<?php
		}
		?>
	</div>
</div>

