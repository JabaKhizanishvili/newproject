<?php

class f_benefitsView extends View
{
	protected $_option = 'f_benefits';
	protected $_option_edit = 'f_benefit';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'f_benefits.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
