<?php

class ReleasetypesView extends View
{
	protected $_option = 'releasetypes';
	protected $_option_edit = 'releasetype';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'releasetypes.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
