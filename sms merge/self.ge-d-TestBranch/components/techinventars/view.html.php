<?php

class techinventarsView extends View
{
	protected $_option = 'techinventars';
	protected $_option_edit = 'techinventar';
	protected $_order = 't.lib_title';
	protected $_dir = '1';
	protected $_space = 'techinventars.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );
	}

}
