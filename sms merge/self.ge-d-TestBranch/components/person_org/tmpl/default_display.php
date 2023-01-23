<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
//$config = $this->getProperties();
$TABLE = $this->data->display_origin;
$TABLE_CHANGED = $this->data->display_changed;
$task = C::_( 'CONFIRMATION', $this->data );
$label = C::_( 'CONFIRMATIONLABEL', $this->data );
unset( $this->data->CONFIRMATION );
unset( $this->data->CONFIRMATIONLABEL );
unset( $this->data->display_origin );
unset( $TABLE->_DATE_FIELDS );
unset( $TABLE->ULEVEL );
unset( $TABLE->QUANTITY );
?>
<div class="page_title">
	<?php echo $label . ' - ' . Text::_( 'confirm' ); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content row">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<?php
		$dinamics = [
				'PERSON' => 'persons',
				'ORG' => 'org',
				'STAFF_SCHEDULE' => 'staffschedule',
//				'CHIEF_SCHEDULE' => 'staffschedule',
//				'REPLACE_SCHEDULE' => 'staffschedule',
				'AUTO_OVERTIME' => 'wstate',
				'GRAPHTYPE' => 'graphtype',
                'ATTRIBUTES' => 'attributelist',
				'SALARYTYPE' => 'salarytypes',
				'CHANGE_SUB_TYPE' => 'changetypes',
//				'JD' => 'jd',
				'CHIEFS' => 'persons',
//				'ORG_PLACE' => 'unit',
				'WORKING_RATE' => 'workingrate',
				'BENEFIT_TYPES' => 'benefittypes',
//				'POSITION' => 'position',
				'CATEGORY_ID' => 'wgroups',
				'ACCOUNTING_OFFICES' => 'offices',
				'SALARY_PAYMENT_TYPE' => [ 1 => 'Cash', 2 => 'Card' ],
				'NATIONALITY' => [ 0 => 'Other Country', 1 => 'Georgia' ],
				'CALCULUS_REGIME' => [ 1 => 'Office', 2 => 'Distance' ],
				'CONTRACT_TYPE' => [ 1 => 'Standard Contract' ],
				'CALCULUS_TYPE' => [ 1 => 'Digital', 2 => 'Material', 3 => 'Excluded' ]
		];

		$lblKey = '';
		if ( count( $TABLE_CHANGED ) )
		{
			foreach ( $TABLE as $TBL )
			{
				if ( C::_( 'PERSON', $TBL ) )
				{
					$lblKey = 'PERSON';
				}
				unset( $TBL->WORKING_RATE );
				unset( $TBL->_DATE_FIELDS );
				unset( $TBL->ULEVEL );
				unset( $TBL->QUANTITY );
				Xhelp::DataBox( $TBL, $dinamics, 'col-md-6', $TABLE_CHANGED[$TBL->ID], $lblKey );
			}
		}
		else
		{
			if ( C::_( 'PERSON', $TABLE ) )
			{
				$lblKey = 'PERSON';
			}
			unset( $TABLE['CONFIRMATIONLABEL'] );
			Xhelp::DataBox( $TABLE, $dinamics, 'col-md-6', $TABLE_CHANGED, $lblKey );
		}
		Xhelp::TransportParams( $this->data );
		?>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<div class="toolbar container">
	<?php
	Helper::getToolbar( 'Save', $this->_option_edit, $task );
	?>
</div>
<?php
$this->setHelp();

