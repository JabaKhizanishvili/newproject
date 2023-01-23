<?php 
class apisView extends View
{
	protected $_option = 'apis';
	protected $_option_edit = 'api';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'apis.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
