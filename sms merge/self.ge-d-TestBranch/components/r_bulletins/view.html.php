<?php

class r_bulletinsView extends View
{
	protected $_option = 'r_bulletins';
	protected $_option_edit = 'r_bulletins';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'r_bulletins.display';

	function display( $tmpl = null )
	{
		/* @var $model r_bulletinsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

	public function SetBData( $Item, &$DayCount, &$UserItem )
	{
		if ( empty( $Item ) )
		{
			return;
		}
		$Status = C::_( 'STATUS', $Item );
		$Day = C::_( 'DAY_COUNT', $Item );
		if ( $Day > 0 )
		{
			$DayCount += $Day;
		}

		switch ( $Status )
		{
			default:
			case 1:
				$UserItem['Current'][] = $Item;
				break;
			case 2:
				$UserItem['Close'][] = $Item;
				break;
			case 3:
				$UserItem['Confirmed'][] = $Item;
				break;
		}

	}

}
