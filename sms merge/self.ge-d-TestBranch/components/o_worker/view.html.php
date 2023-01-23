<?php

class o_workerView extends View
{
	protected $_option = 'o_worker';
	protected $_option_edit = 'o_worker';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'o_worker.display';

	function display( $tmpl = null )
	{
		/* @var $model o_workersModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		if ( $task == 'save' )
		{
			$data = Request::getVar( 'params', array() );
			$model->SaveData( $data );
		}
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
