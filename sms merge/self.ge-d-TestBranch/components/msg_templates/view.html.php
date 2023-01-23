<?php

class Msg_templatesView extends View
{
	protected $_option = 'msg_templates';
	protected $_option_edit = 'msg_template';
	protected $_order = 'lib_title';
	protected $_dir = '0';
	protected $_space = 'msg_templates.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
