<?php

class CWSView extends View
{
	protected $_option = 'cws';
	protected $_option_edit = 'cws';
	protected $_order = 'lastname';
	protected $_dir = '0';
	protected $_space = 'CWS.display';

	function display( $tmpl = null )
	{
		/* @var $model CWSModel */
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
