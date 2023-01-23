<?php
$startDate = new PDate( $this->data->start_date );
$EndingDate = new PDate( $this->data->end_date );
echo HTML::renderFilters( $params, dirname( __FILE__ ) . DS . 'default.xml', $config );
$GroupID = C::_( 'data.group_id', $this );
?>
<div class="tk_fix">
	<div class="tk_list_header_fix">
		<div class="header_workers bulletin_report_item_head">
			<div class="header_workers_in">
				<?php echo Text::_( 'Workers' ); ?>
			</div>
		</div>
		<div class="tk_i_no_scroll"></div>
	</div>
	<?php
	$monthNum = $startDate->toFormat( '%m' );
	$K = 1;
	foreach ( $this->data->workers as $worker )
	{
		if ( $K == 1 )
		{
			?>
			<!--<div class="tk_i_no_scroll"></div>-->
			<?php
		}
		$K++;
		if ( $K > TKCalendar::$step )
		{
			$K = 1;
		}
		?>
		<div class="tk_user r_item_head">
			<div class="tk_user_in_a" id = "tr_<?php echo $worker->ID; ?>_a">
				<?php echo XTranslate::_( $worker->FIRSTNAME, 'person'  ) . ' ' . XTranslate::_( $worker->LASTNAME, 'person'  ); ?>
			</div>
		</div>
		<?php
	}
	?>
</div>
<div class="tk_scroll_block hrgraph_header_fix">
	<div class="tk_scroll_a FloatingScrollbar">
		<div class="tk_scroll_in">
			<?php
			$StartingDate = new PDate( $startDate->toUnix() );
			echo TKCalendar::getHeader( $StartingDate, $EndingDate, '_a' );
			TKCalendar::$countItems = 1;
			foreach ( $this->data->workers as $worker )
			{
				echo TKCalendar::getItems( $StartingDate, $EndingDate, $worker, '_a', $GroupID );
			}
			?>
		</div>
	</div>
</div>
<div class="cls"></div>

