<?php

class r_outsView extends View
{
	protected $_option = 'r_outs';
	protected $_option_edit = 'r_outs';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'r_outs.display';

	function display( $tmpl = null )
	{
		/* @var $model r_outssModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
