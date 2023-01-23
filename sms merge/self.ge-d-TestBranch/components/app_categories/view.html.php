<?php

class App_categoriesView extends View
{
	protected $_option = 'app_categories';
	protected $_option_edit = 'app_category';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'positions.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
