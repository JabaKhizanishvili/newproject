<?php

class PTimesAsHRsView extends View
{
	protected $_option = 'ptimesashrs';
	protected $_option_edit = 'ptimesashr';
	protected $_order = 't.status';
	protected $_dir = '0';
	protected $_space = 'ptimesashrs.display';

	function display( $tmpl = null )
	{
		/* @var $model PTimesAsHRSModel */
		$model = $this->getModel();
		$data = $model->getList();		
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
