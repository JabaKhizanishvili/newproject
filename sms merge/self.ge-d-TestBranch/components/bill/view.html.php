<?php
include(PATH_BASE . DS . 'libraries' . DS . 'Table.php');

class BillView extends View
{
	protected $_option = 'bill';
	protected $_option_edit = 'bill';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'Bill.display';

	function display( $tmpl = null )
	{
		/* @var $model BillModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
