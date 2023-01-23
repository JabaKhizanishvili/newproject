<?php

class UnitTypesView extends View
{
	protected $_option = 'unittypes';
	protected $_option_edit = 'unittype';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'unittypes.display';

	function display( $tmpl = null )
	{
		/* @var $model UnitTypesModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
