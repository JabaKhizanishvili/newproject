<?php

class LinksView extends View
{
	protected $_option = 'links';
	protected $_option_edit = 'link';
	protected $_order = 'title';
	protected $_dir = '0';
	protected $_space = 'links.display';

	function display( $tmpl = null )
	{
		/* @var $model LinksModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
