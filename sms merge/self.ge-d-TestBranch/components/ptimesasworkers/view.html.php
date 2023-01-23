<?php

class PTimesAsWorkersView extends View
{
	protected $_option = 'ptimesasworkers';
	protected $_option_edit = 'ptimesasworker';
	protected $_order = 't.start_date';
	protected $_dir = '1';
	protected $_space = 'ptimesasworkers.display';

	function display( $tmpl = null )
	{
		/* @var $model PTimesAsWorkersModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
