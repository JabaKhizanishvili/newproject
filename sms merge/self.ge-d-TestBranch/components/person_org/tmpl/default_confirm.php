<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
$DATA = C::_( 'DATA', $this->data );
$TASK = C::_( 'TASK', $this->data );
$LABEL = C::_( 'LABEL', $this->data );
$HEADER = C::_( 'HEADER', $this->data );
$DISPLAY_CUT = C::_( 'DISPLAY_CUT', $this->data );
$TRANSPORT_CUT = C::_( 'TRANSPORT_CUT', $this->data );

//$config = $this->getProperties();
?>
<div class="page_title">
	<?php echo $LABEL . ' - ' . Text::_( 'confirm' ); ?>
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
				'CHANGE_SUB_TYPE' => 'changetypes',
//				'JD' => 'jd',
				'CHIEFS' => 'persons',
//				'ORG_PLACE' => 'unit',
				'WORKING_RATE' => 'workingrate',
				'RELEASE_FILES' => 'fileprint',
//				'POSITION' => 'position',
				'CATEGORY_ID' => 'wgroups',
				'ACCOUNTING_OFFICES' => 'offices',
				'SALARY_PAYMENT_TYPE' => [ 1 => 'Cash', 2 => 'Card' ],
				'NATIONALITY' => [ 0 => 'Other Country', 1 => 'Georgia' ],
				'CALCULUS_REGIME' => [ 1 => 'Office', 2 => 'Distance' ],
				'CONTRACT_TYPE' => [ 1 => 'Standard Contract' ],
				'CALCULUS_TYPE' => [ 1 => 'Digital', 2 => 'Material', 3 => 'Excluded' ]
		];

		foreach ( $DATA as $wid => $data )
		{
			static $NN = 0;
			$Display = $data;
			$Transport = $data;

			if ( is_array( $DISPLAY_CUT ) )
			{
				foreach ( $Display as $e => $i )
				{
					if ( in_array( $e, $DISPLAY_CUT ) )
					{
						unset( $Display[$e] );
					}
				}
			}

			Xhelp::DataBox( $Display, $dinamics, 'col-md-6', [], $HEADER );

			$LastSchedule = C::_( '0', Job::checkLastSchedule( $wid ) );
			$Transport['EXTRA_PARAMS'] = '0';

			echo '<div class="container ext_input">';
			if ( $LastSchedule == 1 )
			{
				$NN++;
				$params .= "\n" . HTML::convertParams( [ 'WORKER' => $wid ] );
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_confirm.xml' );
				$Transport['AUTO_PERSON_STATUS_STOP_COUNT'] = $NN;
				$Transport['EXTRA_PARAMS'] = '1';
			}
			echo '</div>';

			if ( is_array( $TRANSPORT_CUT ) )
			{
				foreach ( $Transport as $e => $i )
				{
					if ( in_array( $e, $TRANSPORT_CUT ) )
					{
						unset( $Transport[$e] );
					}
				}
			}
			Xhelp::TransportParams( $Transport );
		}
		?>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<div class="toolbar container">
	<?php
	Helper::getToolbar( 'Save', $this->_option_edit, $TASK );
	?>
</div>
<?php
$this->setHelp();

