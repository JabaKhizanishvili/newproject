<?php

class Staff_schedulesView extends View
{
	protected $_option = 'staff_schedules';
	protected $_option_edit = 'staff_schedule';
	protected $_order = 'lib_title';
	protected $_dir = '0';
	protected $_space = 'staff_schedules.display';

	function display( $tmpl = null )
	{
		/* @var $model UnitsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
