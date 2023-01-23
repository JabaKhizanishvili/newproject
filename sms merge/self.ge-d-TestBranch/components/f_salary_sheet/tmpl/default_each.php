<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
$id = C::_( 'ID', $this->data );
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Save', $this->_option_edit, 'save_each' );
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel_each' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<div class="row">
			<div class="col-md-12">
				<?php
				$html = '';
				$sheet_id = null;
				$worker = null;
				$pay_pension = null;
				foreach ( $this->data as $type => $data )
				{
					$type_name = '';
					if ( $type == 0 )
					{
						$type_name = Text::_( 'SALARY_NET' );
					}

					if ( $type == 1 )
					{
						$type_name = Text::_( 'REGULAR_BENEFIT' ) . ' - ';
					}

					foreach ( $data as $id => $each )
					{
						$spacer = $type_name . $each->BENEFIT_NAME;
						$html .= '<div class="row flx ">';
						$html .= $this->html( 'spacer', 'NAME', $spacer );
						$html .= $this->html( 'text', 'WORKER_SHARE', (float) $each->WORKER_SHARE, '[EDIT][' . $id . '][WORKER_SHARE]' );
						$html .= $type != 0 ? $this->html( 'text', 'COMPANY_SHARE', (float) $each->COMPANY_SHARE, '[EDIT][' . $id . '][COMPANY_SHARE]' ) : '';
						$html .= $this->html( 'hidden', 'TYPE', $type, '[EDIT][' . $id . '][TYPE]' );
						$html .= $this->html( 'hidden', 'DATA_ID', $each->DATA_ID, '[EDIT][' . $id . '][DATA_ID]' );
						$html .= '</div>';
						if ( is_null( $pay_pension ) )
						{
							$pay_pension = $each->PAY_PENSION;
						}
						if ( is_null( $worker ) )
						{
							$worker = $each->WORKER;
						}
						if ( is_null( $sheet_id ) )
						{
							$sheet_id = $each->SHEET_ID;
						}
					}
				}

				$html .= $this->html( 'hidden', 'PAY_PENSION', $pay_pension, '[PAY_PENSION]' );
				$html .= $this->html( 'hidden', 'WORKER', $worker, '[WORKER]' );
				$html .= $this->html( 'hidden', 'SHEET_ID', $sheet_id, '[SHEET_ID]' );
				echo $html;
				?>
			</div>
			<!--<div class="col-md-6">-->
			<!--</div>-->
		</div>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

