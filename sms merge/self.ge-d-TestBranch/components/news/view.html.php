<?php

class newsView extends View
{
	protected $_option = 'news';
	protected $_option_edit = 'new';
	protected $_order = 't.publish_date';
	protected $_dir = '1';
	protected $_space = 'news';

	function display( $tmpl = null )
	{
		/* @var $model newsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
