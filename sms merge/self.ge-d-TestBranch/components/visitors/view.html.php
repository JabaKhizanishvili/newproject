<?php

class VisitorsView extends View
{
	protected $_option = 'visitors';
	protected $_option_edit = 'visitor';
	protected $_order = 't.lib_title';
	protected $_dir = '0';
	protected $_space = 'visitors.display';

	function display( $tmpl = null )
	{
		/* @var $model VisitorsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
