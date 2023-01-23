<?php

class App_typesView extends View
{
	protected $_option = 'app_types';
	protected $_option_edit = 'app_type';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'app_types.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
