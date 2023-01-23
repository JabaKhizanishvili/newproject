<?php

class r_cwrView extends View
{
	protected $_option = 'r_cwr';
	protected $_option_edit = 'r_cwr';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'r_cwr.display';

	function display( $tmpl = null )
	{
		/* @var $model r_cwrModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
