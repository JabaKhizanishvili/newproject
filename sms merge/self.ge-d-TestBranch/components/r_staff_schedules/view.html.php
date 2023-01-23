<?php

class R_staff_schedulesView extends View
{
	protected $_option = 'r_staff_schedules';
	protected $_option_edit = 'r_staff_schedules';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'r_staff_schedules.display';

	function display( $tmpl = null )
	{
		/** @var R_staff_schedulesModel $model */
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
