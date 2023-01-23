<?php

class f_accuracy_periodsView extends View
{
	protected $_option = 'f_accuracy_periods';
	protected $_option_edit = 'f_accuracy_period';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'f_accuracy_periods.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
