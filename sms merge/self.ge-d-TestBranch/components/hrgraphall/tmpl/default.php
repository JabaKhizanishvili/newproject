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
<div class="page_content new_graph">  
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
				<div class="header_workers bulletin_report_item_head row">
					<div class="header_workers_in col-md-4">
						<?php
						$_order = isset( $this->data->order ) ? $this->data->order : '';
						$_dir = isset( $this->data->dir ) ? $this->data->dir : '';
						$class = ' class= "list_header_x" ';
						$order = 'p.lastname';
						?>
						<div <?php echo $class; ?>>
							<span><?php echo Helper::getTableHeader( $order, 'Workers', $_order, $_dir ); ?></span>
						</div>
					</div>
					<div class="header_workers_in col-md-4">
						<?php
						$order1 = 'lss.lib_title';
						?>
						<div <?php echo $class; ?>>
							<span><?php echo Helper::getTableHeader( $order1, 'Staff schedule', $_order, $_dir ); ?></span>
						</div>
					</div>
					<div class="header_workers_in col-md-4">
						<?php
						$order2 = 'u.lib_title';
						?>
						<div <?php echo $class; ?>>
							<span><?php echo Helper::getTableHeader( $order2, 'Unit', $_order, $_dir ); ?></span>
						</div>
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
					<div class="tk_user r_item_head ">
						<div class="tk_user_in row" id = "tr_<?php echo $worker->ID; ?>">
							<div class="col-md-4 fixed-block">
								<?php echo XTranslate::_( $worker->FIRSTNAME, 'person' ) . ' ' . XTranslate::_( $worker->LASTNAME, 'person' ); ?>
							</div>
							<div class="col-md-4 fixed-block">
								<?php echo Helper::MakeDoubleToolTip( $worker->SCH_NAME, $worker->SCH_NAME ); ?>
							</div>
							<div class="col-md-4 fixed-block">
								<?php echo Helper::MakeDoubleToolTip( $worker->ORG_PLACE_TITLE, $worker->ORG_PLACE_TITLE ); ?>
							</div>
						</div>
					</div>
					<div class="cls"></div>
				</div>
				<?php
			}

			///Display Time Data
			$GraphTimeData = TKCalendar::GetGraphTimesData();
			?>
		</div>
		<div class = "tk_scroll_block hrgraph_header_fix">
			<div class = "tk_scroll FloatingScrollbar">
				<div class = "tk_scroll_in">
					<?php
					$StartingDate = new PDate( $startDate->toUnix() );
					$EndingDate = new PDate( $this->data->end_date );
					echo TKCalendar::getHeader( $StartingDate, $EndingDate );
					?>
					<div class="tk_day_data">
						<?php
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
			<?php
			echo TKCalendar::GetGraphTimes();
			?>
		</div>
		<div class="footer_block">
			<?php
			$total = isset( $this->data->total ) ? $this->data->total : '';
			$start = isset( $this->data->start ) ? $this->data->start : '';
			$paging = Pagination::Generate( $total, $start );
			if ( !empty( $paging ) )
			{
				echo $paging;
			}
			?>		
		</div>
		<div class="cls"></div>
		<br />
		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
		<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
		<input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
		<input type="hidden" value="" name="task" /> 
	</form>
</div>
