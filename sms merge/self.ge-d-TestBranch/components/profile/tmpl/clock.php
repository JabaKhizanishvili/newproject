<div class="server-time">
	<div class="row">
		<div class="col-md-4 col-xs-12  col-sm-5 nopadding">
			<div id="clock" class="light">
				<div class="display">
					<div class="digits"></div>
				</div>
			</div>
		</div>
		<div class="col-xs-12  col-sm-7 nopadding">
			<div class="server-time-date">
				<?php echo PDate::Get()->toFormat( '%A, %d.%m.%Y' ); ?> <?php echo Text::_('Y');?>
			</div>
		</div>
	</div>
	<div class="cls"></div>
</div>
<?php
$sql = 'select (sysdate - date \'1970-01-01\') * 60 * 60 * 24  - 14400 timestamp from dual';
$date = (int) DB::LoadResult( $sql );
Helper::SetJS( '$("#clock").clock({timestamp:' . $date . '});' );

