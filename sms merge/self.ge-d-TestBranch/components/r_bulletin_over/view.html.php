<?php

class r_bulletin_overView extends View
{
	protected $_option = 'r_bulletin_over';
	protected $_option_edit = 'r_bulletin_over';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'r_bulletin_over.display';

	function display( $tmpl = null )
	{
		/* @var $model r_bulletin_oversModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
