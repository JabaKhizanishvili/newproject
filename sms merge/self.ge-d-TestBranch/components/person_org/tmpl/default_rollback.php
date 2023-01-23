<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
$page_title = Text::_( 'rollback' );
?>
<div class="page_title">
	<?php
	echo $page_title;
	?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Save', $this->_option_edit, 'save_rollback', 0, 1 );
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<div class="row underline">
			<div class="col-md-6">
				<?php
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_rollback.xml' );
				?>
			</div>
			<div class="col-md-6">
				<?php
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_rollback.xml', 'params', 'more' );
				?>
			</div>
		</div>
		<div class="row">
			<?php
			$dinamic = [
					'PERSON' => 'persons',
					'ORG' => 'org',
					'STAFF_SCHEDULE' => 'staffschedule',
					'CHANGE_SCHEDULE' => 'staffschedule',
					'ACCOUNTING_OFFICE' => 'offices',
					'AUTO_OVERTIME' => 'wstate',
					'GRAPHTYPE' => 'graphtype',
					'SALARYTYPE' => 'salarytypes',
					'CHANGE_SUB_TYPE' => 'changetypes',
					'CHIEFS' => 'persons',
					'ORG_PLACE' => 'unit',
					'WORKING_RATE' => 'workingrate',
					'BENEFIT_TYPES' => 'benefittypes',
					'CATEGORY_ID' => 'wgroups',
					'ACCOUNTING_OFFICES' => 'offices',
					'SALARY_PAYMENT_TYPE' => [ 1 => 'Cash', 2 => 'Card' ],
					'NATIONALITY' => [ 0 => 'Other Country', 1 => 'Georgia' ],
					'CALCULUS_REGIME' => [ 1 => 'Office', 2 => 'Distance' ],
					'CONTRACT_TYPE' => [ 1 => 'Standard Contract' ],
					'CALCULUS_TYPE' => [ 1 => 'Digital', 2 => 'Material', 3 => 'Excluded' ],
					'CHANGE_TYPE' => [ 1 => 'assignment', 2 => 'changing', 3 => 'release', 4 => 'benefits', 5 => 'schedulechanging', 6 => 'schedulechanging' ]
			];

			$cut = [
					'ID',
					'CHANGE_SUB_TYPE',
					'CLIENT_ID',
					'CHANGE_ID',
					'CHANGE_DATE',
					'CHANGE_TYPE',
					'WORKING_RATE',
					'_DATE_FIELDS',
					'ULEVEL',
					'WORKER_ID',
					'QUANTITY',
					'TOKEN'
			];

			$data1 = [];
			if ( !empty( C::_( 'CURRENT', $this->data ) ) )
			{
				$data1 = (array) C::_( 'CURRENT', $this->data );
			}

			$data2 = [];
			if ( !empty( C::_( 'PREVIOUS', $this->data ) ) )
			{
				$data2 = (array) C::_( 'PREVIOUS', $this->data );
			}

			$label1 = Text::_( 'current data' );
			$label2 = Xhelp::caseText( C::_( 'CHANGE_TYPE', $data2, 1 ), C::_( 'CHANGE_TYPE', $dinamic ) ) . ' - ' . PDate::Get( C::_( 'CHANGE_DATE', $data2 ) )->toFormat( '%Y-%m-%d' );

			Xhelp::data_compare( $data1, $data2, $label1, $label2, $dinamic, $cut );
			?>
		</div>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

