<?php

class r_sms_serviceView extends View
{
	protected $_option = 'r_sms_service';
	protected $_option_edit = 'r_sms_service';
	protected $_order = 'log_date';
	protected $_dir = '1';
	protected $_space = 'r_sms_service.display';

	function display( $tmpl = null )
	{
		/* @var $model r_sms_serviceModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
