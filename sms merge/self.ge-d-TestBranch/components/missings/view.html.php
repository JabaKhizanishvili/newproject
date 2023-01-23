<?php

class MissingsView extends View
{
	protected $_option = 'missings';
	protected $_option_edit = 'missing';
	protected $_order = 't.start_date';
	protected $_dir = '1';
	protected $_space = 'missings.display';

	function display( $tmpl = null )
	{
		/* @var $model overtimeworkersModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
