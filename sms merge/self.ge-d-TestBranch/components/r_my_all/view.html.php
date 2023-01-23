<?php

class r_my_allView extends View
{
	protected $_option = 'r_my_all';
	protected $_option_edit = 'r_my_all';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'r_my_all.display';

	function display( $tmpl = null )
	{
		/* @var $model r_my_allModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
