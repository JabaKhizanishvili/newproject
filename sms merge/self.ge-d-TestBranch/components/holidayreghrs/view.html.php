<?php

class HolidayRegHRsView extends View
{
	protected $_option = 'holidayreghrs';
	protected $_option_edit = 'holidayreghr';
	protected $_order = 't.rec_date';
	protected $_dir = '1';
	protected $_space = 'holidayregdisplay';

	function display( $tmpl = null )
	{
		/* @var $model HolidayRegHRsModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		switch ( $task )
		{
			case 'export':				
				if ( $model->Export() )
				{
					$link = '?option=' . $this->_option;
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;
		}
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
