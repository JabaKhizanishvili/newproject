<?php

class r_myView extends View
{
	protected $_option = 'r_my';
	protected $_option_edit = 'r_my';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'r_my.display';

	function display( $tmpl = null )
	{
		/* @var $model r_myModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
