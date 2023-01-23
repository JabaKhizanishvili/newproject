<?php

class unitorgsView extends View
{
	protected $_option = 'unitorgs';
	protected $_option_edit = 'unitorg';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'unitorgs.display';

	function display( $tmpl = null )
	{
		/* @var $model unitorgsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
