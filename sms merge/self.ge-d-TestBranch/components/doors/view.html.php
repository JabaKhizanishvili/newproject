<?php

class DoorsView extends View
{
	protected $_option = 'doors';
	protected $_option_edit = 'door';
	protected $_order = 'lib_title';
	protected $_dir = '0';
	protected $_space = 'doors.display';

	function display( $tmpl = null )
	{
		/* @var $model DoorsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
