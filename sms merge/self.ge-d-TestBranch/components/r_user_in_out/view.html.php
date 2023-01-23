<?php

// Edited by Irakli Gzirishvili 21-10-2021.

class R_user_in_outView extends View
{
	protected $_option = 'r_user_in_out';
	protected $_option_edit = 'r_user_in_out';
	protected $_order = ' t.log_date ';
	protected $_dir = '1';
	protected $_space = 'r_user_in_out.display';

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
