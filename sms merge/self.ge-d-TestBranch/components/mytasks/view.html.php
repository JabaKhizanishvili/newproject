<?php

class MyTasksView extends View
{
	protected $_option = 'mytasks';
	protected $_option_edit = 'mytask';
	protected $_order = 't.task_create_date';
	protected $_dir = '1';
	protected $_space = 'mytasks.display';

	function display( $tmpl = null )
	{
		/* @var $model MyTaskModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
