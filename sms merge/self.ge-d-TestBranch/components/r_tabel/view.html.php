<?php

class r_tabelView extends View
{
	protected $_option = 'r_tabel';
	protected $_option_edit = 'r_tabel';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'r_tabel.display';

	function display( $tmpl = null )
	{
		/* @var $model r_tabelsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
