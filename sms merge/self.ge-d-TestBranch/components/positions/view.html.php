<?php

class PositionsView extends View
{
	protected $_option = 'positions';
	protected $_option_edit = 'position';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'positions.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
