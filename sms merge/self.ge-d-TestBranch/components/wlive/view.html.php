<?php

class WLiveView extends View
{
	protected $_option = 'wlive';
	protected $_option_edit = 'wlive';
	protected $_order = 'lastname';
	protected $_dir = '0';
	protected $_space = 'live.display';

	function display( $tmpl = null )
	{
		/* @var $model WLiveModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

	public function CalculateStatus( $User )
	{
		$RealTypeID = C::_( 'REAL_TYPE_ID', $User );
		$StatusID = C::_( 'STATUS_ID', $User );
		switch ( $StatusID )
		{
			case 1:
			case 11:
				return 'st_staff_in';
			case 10:
				return 'cl_orange';
			default:
				return $this->CalculateSubStatus( $User );
		}

	}

//	public function CalculateStatus( $User )
//	{
//		$RealTypeID = C::_( 'REAL_TYPE_ID', $User );
//		$StatusID = C::_( 'STATUS_ID', $User );
//		switch ( $RealTypeID )
//		{
//			case 1500:
//				return 'st_day_off';
//			case 2000:
//				if ( $StatusID == 1 )
//				{
//					return 'st_staff_in';
//				}
//				else
//				{
//					return $this->CalculateSubStatus( $User );
//				}
//			case 2500:
//				return 'st_day_off';
//			case 3000:
//				return 'st_day_off';
//			case 3500:
//				return 'st_day_off';
//		}
//
//		return 'st_day_off';
//
//	}

	public function CalculateSubStatus( $User )
	{
		$Type = C::_( 'TYPE', $User );
		switch ( $Type )
		{
			case null:
				break;
			case 0:
			case 1:
			case 3:
			case 4:
				return 'cl_blue';
			case 3:
			case 4:
			case 9:
				return 'cl_9400D3';
			case 5:
				return 'cl_grey';
			case 6:
				return 'cl_black';
			case 2:
				return 'cl_yellow';
			case 7:
				return 'cl_coffe';
		}
		$RealTypeID = C::_( 'REAL_TYPE_ID', $User );
		switch ( $RealTypeID )
		{
			case 1500:
				return 'st_day_off';
			case 2000:
				return 'st_not_in';
			case 2500:
				return 'st_day_off';
			case 3000:
				return 'st_not_in';
			case 3500:
				return 'st_day_off';
		}
		return 'st_not_in';

	}

	public function getToolBar( $params )
	{
		ob_start();
		?>
		<div class="toolbar_block">
			<?php
			foreach ( $params as $param )
			{
				?>
				<a href="javascript:void(0);" class="toolbat_item key_<?php echo $param['class']; ?>" rel="<?php echo $param['class']; ?>" >
					<span class="toolbat_item_in">
						<span class="toolbar_item_count">
							<?php echo $param['count']; ?>
						</span>
						<span class="toolbar_item_lab">
							<?php echo $param['name']; ?>
						</span>			
					</span>
				</a>
				<?php
			}
			?>
			<div class="cls"></div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

}
