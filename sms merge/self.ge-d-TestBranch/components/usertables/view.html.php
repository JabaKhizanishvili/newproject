<?php

class usertablesView extends View
{
	protected $_option = 'usertables';
	protected $_option_edit = 'usertable';
	protected $_order = 'bill_id';
	protected $_dir = '1';
	protected $_space = 'userstables.display';

	function display( $tmpl = null )
	{
		/* @var $model usertablesModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );		
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
