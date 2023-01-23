<?php

class GlobalGraphsView extends View
{
	protected $_option = 'globalgraphs';
	protected $_option_edit = 'globalgraph';
	protected $_order = 'lib_title';
	protected $_dir = '0';
	protected $_space = 'globalgraphs.display';

	function display( $tmpl = null )
	{
		/* @var $model GlobalGraphsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
