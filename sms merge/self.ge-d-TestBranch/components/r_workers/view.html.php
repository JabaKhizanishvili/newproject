<?php

class r_workersView extends View
{
	protected $_option = 'r_workers';
	protected $_option_edit = 'r_workers';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'r_workers.display';

	function display( $tmpl = null )
	{
		/* @var $model r_workersModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
