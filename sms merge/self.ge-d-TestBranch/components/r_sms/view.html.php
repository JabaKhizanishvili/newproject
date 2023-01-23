<?php

class r_smsView extends View
{
	protected $_option = 'r_sms';
	protected $_option_edit = 'r_sms';
	protected $_order = 'log_date';
	protected $_dir = '1';
	protected $_space = 'r_sms.display';

	function display( $tmpl = null )
	{
		/* @var $model r_smsModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		switch ( $task )
		{
			case 'export':
				if ( $model->Export() )
				{
					$link = '?option=' . $this->_option;
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;
		}
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
