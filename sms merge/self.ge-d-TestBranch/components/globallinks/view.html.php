<?php

class GlobalLinksView extends View
{
	protected $_option = 'globallinks';
	protected $_option_edit = 'globallink';
	protected $_order = 'title';
	protected $_dir = '0';
	protected $_space = 'globallinks.display';

	function display( $tmpl = null )
	{
		/* @var $model GlobalLinksModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
