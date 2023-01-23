<?php

class F_salary_typesView extends View
{
	protected $_option = 'f_salary_types';
	protected $_option_edit = 'f_salary_type';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'f_salary_types.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
