<?php

class UnitsView extends View
{
	protected $_option = 'units';
	protected $_option_edit = 'unit';
	protected $_order = 'lib_title';
	protected $_dir = '0';
	protected $_space = 'units.display';

	function display( $tmpl = null )
	{
		/* @var $model UnitsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
