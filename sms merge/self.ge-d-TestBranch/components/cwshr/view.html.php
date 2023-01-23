<?php

class CWSHRView extends View
{
	protected $_option = 'cwshr';
	protected $_option_edit = 'cwshr';
	protected $_order = 'w.lastname';
	protected $_dir = '0';
	protected $_space = 'CWSHR.display';

	function display( $tmpl = null )
	{
		/* @var $model CWSModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
