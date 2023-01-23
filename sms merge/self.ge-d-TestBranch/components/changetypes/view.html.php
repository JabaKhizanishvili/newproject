<?php

class ChangetypesView extends View
{
	protected $_option = 'changetypes';
	protected $_option_edit = 'changetype';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'changetypes.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
