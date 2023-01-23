<?php
// Edited by Irakli Gzirishvili 21-10-2021.

class r_login_logView extends View
{
	protected $_option = 'r_login_log';
	protected $_option_edit = 'r_login_log';
	protected $_order = 'w.log_user_name';
	protected $_dir = '1';
	protected $_space = 'r_login_log.display';

	function display( $tmpl = null )
	{
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
