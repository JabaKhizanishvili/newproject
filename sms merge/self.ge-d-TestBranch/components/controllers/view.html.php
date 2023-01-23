<?php

class controllersView extends View
{
	protected $_option = 'controllers';
	protected $_option_edit = 'controller';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'controllers.display';

	/* @var $model controllersModel */
	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );

		if ( $tmpl == 'modal' )
		{
			$device_id = Request::getVar( 'device_id', '' );
			$action = Request::getVar( 'action', '' );
			$data = $model->getHistory( $device_id, $action );
		}
		else
		{
			switch ( $task )
			{
				case 'undefined_controllers':
					$data = $model->getUndefineds();
					$tmpl = 'undefined';
					break;

				case 'back_to_controllers':
					$link = '?option=' . $this->_option;
					Users::Redirect( $link );
					break;

				default:
					$data = $model->getList();
			}
		}

		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
