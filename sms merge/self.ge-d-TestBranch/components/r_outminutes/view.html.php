<?php

class r_outminutesView extends View
{
	protected $_option = 'r_outminutes';
	protected $_option_edit = 'r_outminutes';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'r_outminutes.display';

	function display( $tmpl = null )
	{
		/* @var $model r_outminutessModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
