<?php

class systemsView extends View
{
	protected $_option = 'systems';
	protected $_option_edit = 'system';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'systems.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
