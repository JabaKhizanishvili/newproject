<?php

class newsitemView extends View
{
	protected $_option = 'newsitem';
	protected $_option_edit = 'newsitem';
	protected $_order = 't.publish_date';
	protected $_dir = '1';
	protected $_space = 'news';

	function display( $tmpl = null )
	{
		/* @var $model newsitemModel */
		$model = $this->getModel();
		$data = $model->getList();
		$news = $model->getItem();
		$this->assignRef( 'data', $data );
		$this->assignRef( 'news', $news );
		parent::display( $tmpl );

	}

}
