<?php

class ETypesView extends View
{
	protected $_option = 'etypes';
	protected $_option_edit = 'etype';
	protected $_order = 'lib_title';
	protected $_dir = '0';
	protected $_space = 'etypes.display';

	function display( $tmpl = null )
	{
		/* @var $model ETypesModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
