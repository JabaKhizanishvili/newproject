<?php

class certificatesView extends View
{
	protected $_option = 'certificates';
	protected $_option_edit = 'certificate';
	protected $_order = 'worker';
	protected $_dir = '0';
	protected $_space = 'certificates.display';

	function display( $tmpl = null )
	{
		/* @var $model covidModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		$data = array();
		switch ( $task )
		{
			case 'export':
				return $model->Export();
			default:
				$data = $model->getList();
		}
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
