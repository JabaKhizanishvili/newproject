<?php

class FlowElementsView extends View
{
	protected $_option = 'flowelements';
	protected $_option_edit = 'flowelement';
	protected $_order = 't.lib_level';
	protected $_dir = '0';
	protected $_space = 'flowelements.display';

	function display( $tmpl = null )
	{
		/* @var $model FlowElementsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
