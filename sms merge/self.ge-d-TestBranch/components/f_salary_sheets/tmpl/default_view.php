<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$this->Set( 'disable_config', true );
$Benefits = $this->data->benefits;

$Categories = $this->data->categories;
?>
<div class="page_title">
	<?php echo Helper::getPageTitle( $this->data->sheet_name ); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Edit', $this->_option_edit, 'edit_each', 1 );
		Helper::getJSToolbar( 'Export To Exel', 'exportTableToExcel', array( '#exportable' ) );
		Helper::getToolbar( 'Delete', $this->_option_edit, 'delete_each', 1, 1 );
		Helper::getToolbar( 'Back', $this->_option, 'back' );
		?>
	</div>
	<div class="cls"></div>
</div>

<div class="page_content">
	<form action="" method="get" name="fform" id="fform" class="form-horizontal">
		<?php
//		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default_view.xml', $config );
		if ( count( $this->data->items ) )
		{
			?>
			<div class="report_page_result">
				<div class="double-scroll FloatingScrollbar">
					<table class="table table-bordered table-condensed" id="exportable">
						<?php
//						Header _----------------------------------------------------------------------------------------------------
						$th = [];
						$th[] = $this->Tpart( 'th', '#', 'text-center', 'row', 2 );
						$th[] = $this->Tpart( 'rh', '', 'text-center', 'row', 2 );
						$th[] = $this->Tpart( 'th', 'worker', 'text-center', 'row', 2, 1 );
						$th[] = $this->Tpart( 'th', 'private_number', 'text-center', 'row', 2, 1 );
						$th[] = $this->Tpart( 'th', 'staff_schedule', 'text-center', 'row', 2, 1 );
						$th[] = $this->Tpart( 'th', 'schedule_code', 'text-center', 'row', 2, 1, 1 );
						$th[] = $this->Tpart( 'th', 'position', 'text-center', 'row', 2, 1, 1 );
						$th[] = $this->Tpart( 'th', 'unit', 'text-center', 'row', 2, 1, 1 );
						$th[] = $this->Tpart( 'th', 'unit_code', 'text-center', 'row', 2, 1, 1 );
						$th[] = $this->Tpart( 'th', 'main_unit', 'text-center', 'row', 2, 1, 1 );
						$th[] = $this->Tpart( 'th', 'tablenum', 'text-center', 'row', 2, 1, 1 );
						$th[] = $this->Tpart( 'th', 'worker_code', 'text-center', 'row', 2, 1, 1 );
						$th[] = $this->Tpart( 'th', 'assignment_date', 'text-center', 'row', 2, 1, 1 );
						$th[] = $this->Tpart( 'th', 'company_assignment_date', 'text-center', 'row', 2, 1, 1 );
						$th[] = $this->Tpart( 'th', 'salary', 'text-center', 'row', 2, 1 );
						$th[] = $this->Tpart( 'th', 'salary_net', 'text-center', 'row', 2, 1 );

						$fth = [];
						$all_fields = [];
						$all_fields_count = 0;
						foreach ( $Categories as $id => $data )
						{
							$fields = C::_( 'FIELDS', $data );
							$th[] = $this->Tpart( 'th', C::_( 'NAME', $data ), 'text-center', 'col', count( $fields ) );
							foreach ( $fields as $name )
							{
								$fth[] = $this->Tpart( 'th', $name, 'text-center', 'row', 2, 1 );
								$all_fields[$id][] = $name;
								$all_fields_count++;
							}
						}

						$th[] = $this->Tpart( 'th', 'taxable_sum', 'text-center', 'row', 2, 1 );
						$th[] = $this->Tpart( 'th', 'income_tax_sum', 'text-center', 'row', 2, 1 );
						$th[] = $this->Tpart( 'th', 'worker_pension_tax_sum', 'text-center', 'row', 2, 1 );
						$th[] = $this->Tpart( 'th', 'company_pension_tax_sum', 'text-center', 'row', 2, 1 );
						$th[] = $this->Tpart( 'th', 'pension_tax_sum', 'text-center', 'row', 2, 1 );
						$th[] = $this->Tpart( 'th', 'full_sum', 'text-center', 'row', 2, 1 );
						$th[] = $this->Tpart( 'th', 'pay_sum', 'text-center', 'row', 2, 1 );
						$this->Tpart( 'tr', implode( '', $th ) );
						$this->Tpart( 'tr', implode( '', $fth ) );

//						Items ----------------------------------------------------------------------------------------------------
						$income_tax_sum = 0;
						$pension_tax_sum = 0;
						$full_sum = 0;
						$pay_sum = 0;

						$count = null;
						$this->Tpart( 'tr', '' );
						foreach ( $this->data->items as $n => $item )
						{
							$td = [];
							$td[] = $this->Tpart( 'td', $n + 1, 'text-center' );
							$td[] = $this->Tpart( 'r', $item->ID . '|' . $this->data->sheet_id, 'text-center' );
							$td[] = $this->Tpart( 'a', $item->ID . '&sheet_id=' . $this->data->sheet_id . '|' . $item->WORKER . '|edit_each|' . $this->_option_edit, 'text-left' );
							$td[] = $this->Tpart( 'td', $item->PRIVATE_NUMBER, 'text-left' );
							$td[] = $this->Tpart( 'td', $item->STAFF_SCHEDULE, 'text-left' );
							(Helper::getConfig( 'salary_sheet_schedule_code' )) ? $td[] = $this->Tpart( 'td', $item->SCHEDULE_CODE, 'text-left', '', '', 0, 1, 'schedule_code' ) : null;
							(Helper::getConfig( 'salary_sheet_position' )) ? $td[] = $this->Tpart( 'td', $item->POSITION, 'text-left', '', '', 0, 1, 'position' ) : null;
							(Helper::getConfig( 'salary_sheet_unit' )) ? $td[] = $this->Tpart( 'td', $item->UNIT, 'text-left', '', '', 0, 1, 'unit' ) : null;
							(Helper::getConfig( 'salary_sheet_unit_code' )) ? $td[] = $this->Tpart( 'td', $item->UNIT_CODE, 'text-left', '', '', 0, 1, 'unit_code' ) : null;
							(Helper::getConfig( 'salary_sheet_main_unit' )) ? $td[] = $this->Tpart( 'td', $item->MAIN_UNIT, 'text-left', '', '', 0, 1, 'main_unit' ) : null;
							(Helper::getConfig( 'salary_sheet_tablenum' )) ? $td[] = $this->Tpart( 'td', $item->TABLENUM, 'text-left', '', '', 0, 1, 'tablenum' ) : null;
							(Helper::getConfig( 'salary_sheet_worker_code' )) ? $td[] = $this->Tpart( 'td', $item->WORKER_CODE, 'text-left', '', '', 0, 1, 'worker_code' ) : null;
							(Helper::getConfig( 'salary_sheet_assignment_date' )) ? $td[] = $this->Tpart( 'td', $item->ASSIGNMENT_DATE, 'text-left', '', '', 0, 1, 'assignment_date' ) : null;
							(Helper::getConfig( 'salary_sheet_company_assignment_date' )) ? $td[] = $this->Tpart( 'td', $item->COMPANY_ASSIGNMENT_DATE, 'text-left', '', '', 0, 1, 'company_assignment_date' ) : null;
							$td[] = $this->Tpart( 'td', (float) $item->SALARY, 'text-left' );
							$td[] = $this->Tpart( 'td', helper::formatBalance( $item->SALARY_NET, 2 ), 'text-left' );
							foreach ( $all_fields as $category => $fields )
							{
								$data = C::_( $item->ID . '.' . $category, $Benefits, [] );
								foreach ( $fields as $each )
								{
									$value = C::_( $each, $data, 0 );
									$td[] = $this->Tpart( 'td', $value, 'text-left' );
								}
							}

							$td[] = $this->Tpart( 'td', Helper::FormatBalance( $item->TAXABLE_SUM, 2 ), 'text-left' );
							$td[] = $this->Tpart( 'td', Helper::FormatBalance( $item->INCOME_TAX_SUM, 2 ), 'text-left' );
							$td[] = $this->Tpart( 'td', Helper::FormatBalance( $item->WORKER_PENSION_TAX_SUM, 2 ), 'text-left' );
							$td[] = $this->Tpart( 'td', Helper::FormatBalance( $item->COMPANY_PENSION_TAX_SUM, 2 ), 'text-left' );
							$td[] = $this->Tpart( 'td', Helper::FormatBalance( $item->PENSION_TAX_SUM, 2 ), 'text-left' );
							$td[] = $this->Tpart( 'td', Helper::FormatBalance( $item->FULL_SUM, 2 ), 'text-left' );
							$td[] = $this->Tpart( 'td', Helper::FormatBalance( $item->PAY_SUM, 2 ), 'text-left' );

							$income_tax_sum += $item->INCOME_TAX_SUM;
							$pension_tax_sum += $item->PENSION_TAX_SUM;
							$full_sum += $item->FULL_SUM;
							$pay_sum += $item->PAY_SUM;

							if ( is_null( $count ) )
							{
								$count = count( $td );
							}

							$this->Tpart( 'tr', implode( '', $td ) );
						}

						$sum = [];
						for ( $i = 1; $i <= $count - 6; $i++ )
						{
							$sum[] = $this->Tpart( 'td', '', 'text-left', 'row', 2 );
						}

						$sum[] = $this->Tpart( 'td', Helper::FormatBalance( $income_tax_sum, 2 ), 'text-center', 'row', 2 );
						$sum[] = $this->Tpart( 'td', '', 'text-left', 'row', 2 );
						$sum[] = $this->Tpart( 'td', '', 'text-left', 'row', 2 );
						$sum[] = $this->Tpart( 'td', Helper::FormatBalance( $pension_tax_sum, 2 ), 'text-center', 'row', 2 );
						$sum[] = $this->Tpart( 'td', Helper::FormatBalance( $full_sum, 2 ), 'text-center', 'row', 2 );
						$sum[] = $this->Tpart( 'td', Helper::FormatBalance( $pay_sum, 2 ), 'text-center', 'row', 2 );

						$this->Tpart( 'tr', implode( '', $sum ), 'sumrow' );
						?>
					</table>
				</div>
				<tfoot>
					<?php
					$total = isset( $this->data->total ) ? $this->data->total : '';
					$start = isset( $this->data->start ) ? $this->data->start : '';
					$paging = Pagination::Generate( $total, $start );
					if ( empty( $total ) )
					{
						return;
					}
					?>
					<tr>
						<td colspan="50">
							<div class="footer_block">
								<?php
								if ( !empty( $paging ) )
								{
									echo $paging;
								}
								?>		
							</div>
						</td>
					</tr>
				</tfoot>
			</div>
		<?php } ?>
		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
		<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
		<input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
		<input type="hidden" value="<?php echo Request::getVar( 'task', 'view' ); ?>" name="task" />
	</form>
</div>
<?php
$this->setHelp();
