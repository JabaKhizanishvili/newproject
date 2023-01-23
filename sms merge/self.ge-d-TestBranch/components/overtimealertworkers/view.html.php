<?php

class overtimeworkersView extends View
{
	protected $_option = 'overtimeworkers';
	protected $_option_edit = 'overtimeworker';
	protected $_order = 't.start_date';
	protected $_dir = '1';
	protected $_space = 'overtimeworkers.display';

	function display( $tmpl = null )
	{
		/* @var $model overtimeworkersModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
