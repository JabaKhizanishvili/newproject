<?php

class WorkersInfoView extends View
{
	protected $_option = 'workersinfo';
	protected $_option_edit = 'workersinfo';
	protected $_order = 'wfirstname';
	protected $_dir = '0';
	protected $_space = 'WorkersInfo.display';

	function display( $tmpl = null )
	{
		/* @var $model WorkersInfoModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		$data = array();
		switch ( $task )
		{
			case 'export':
				return $model->ExportData();
			default:
				$data = $model->getList();
				break;
		}
		if ( !is_object( $data ) )
		{
			$data = (object) $data;
		}
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
