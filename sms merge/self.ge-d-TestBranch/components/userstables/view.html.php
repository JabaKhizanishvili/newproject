<?php

class userstablesView extends View
{
	protected $_option = 'userstables';
	protected $_option_edit = 'userstable';
	protected $_order = 'w.firstname';
	protected $_dir = '1';
	protected $_space = 'userstables.display';

	function display( $tmpl = null )
	{
		/* @var $model userstablesModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
