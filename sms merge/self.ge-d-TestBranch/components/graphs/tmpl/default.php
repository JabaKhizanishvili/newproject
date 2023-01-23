<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );

TKCalendar::$step = 10;
$CopyParams = $params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
if ( $this->data->items )
{
	$CopyParams = HTML::convertParams( $this->data->items );
}
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="cls"></div>
</div>
<div class="page_content">  
	<form action="?option=<?php echo $this->_option; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<?php
		$startDate = new PDate( $this->data->start_date );
		echo HTML::renderFilters( $params, dirname( __FILE__ ) . DS . 'default.xml', $config );
		$GroupID = C::_( 'group_id', $this->data );
		?>
		<div class="tk_fix">
			<div class="tk_list_header_fix">
				<div class="tk_list_header_x">
					<div class="radio">
						<input type="checkbox" id="checknid" name="nids">
						<label for="checknid"></label>
					</div>
				</div>
				<div class="header_workers bulletin_report_item_head">
					<div class="header_workers_in">
						<?php echo Text::_( 'Workers' ); ?>
					</div>
				</div>
				<div class="tk_i_no_scroll"></div>
			</div>
			<?php
			$K = 1;
			foreach ( $this->data->workers as $worker )
			{
				$K++;
				if ( $K > TKCalendar::$step )
				{
					$K = 1;
				}
				?>
				<div class="tk_worker_title">
					<div class="tk_list_worker_x">
						<div class="radio">
							<input type="checkbox" value="<?php echo $worker->ID; ?>" id="checknid<?php echo $worker->ID; ?>" name="nid[]" class="self-color self-border checknid">
							<label for="checknid<?php echo $worker->ID; ?>"></label>
						</div>	
					</div>	
					<div class="tk_user r_item_head">
						<div class="tk_user_in" id = "tr_<?php echo $worker->ID; ?>">
							<?php echo XTranslate::_( $worker->FIRSTNAME, 'person' ) . ' ' . XTranslate::_( $worker->LASTNAME, 'person' ); ?>
						</div>
					</div>
					<div class="cls"></div>
				</div>
				<?php
			}

			///Display Time Data
			$GraphTimeData = TKCalendar::GetGraphTimesData();
			?>
			<div class="tk_i_separator"></div>
		</div>
		<div class = "tk_scroll_block hrgraph_header_fix">
			<div class = "tk_scroll FloatingScrollbar">
				<div class = "tk_scroll_in">
					<div class="tk_day_data">
						<?php
						$StartingDate = new PDate( $startDate->toUnix() );
						$EndingDate = new PDate( $this->data->end_date );
						echo TKCalendar::getHeader( $StartingDate, $EndingDate );
						TKCalendar::$countItems = 1;
						foreach ( $this->data->workers as $worker )
						{
							echo TKCalendar::getItems( $StartingDate, $EndingDate, $worker, null, $GroupID );
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<div id="timeGraph">
			<?php echo TKCalendar::GetGraphTimes( $GroupID ); ?>
		</div>
		<div class="cls"></div>
		<br />
		<br />
		<br />
		<br />
		<div class="container">
			<h2 class="text-center">
				<?php echo Text::_( 'Graph Copy' ) ?>
				<div class="toolbar">
					<?php
//					Helper::getToolbar( 'Copy', $this->_option_edit, 'copydata' );
					?>
				</div>
				<div class="cls"></div>
			</h2>
			<?php
			echo HTML::renderParams( $CopyParams, dirname( __FILE__ ) . DS . 'copy.xml' );
			?>
			<div class="">
				<div class="toolbar  text-center">
					<?php
					Helper::getToolbar( 'Copy', $this->_option_edit, 'copydata', 0, 1 );
					?>
				</div>
				<div class="cls"></div>
			</div>
		</div>
		<input type="hidden" value="" name="task" /> 
	</form>
</div>
