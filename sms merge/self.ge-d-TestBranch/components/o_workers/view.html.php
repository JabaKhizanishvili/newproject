<?php

class o_workersView extends View
{
	protected $_option = 'o_workers';
	protected $_option_edit = 'o_workers';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'o_workers.display';

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
