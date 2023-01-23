<?php

class transfersallView extends View
{
	protected $_option = 'transfersall';
	protected $_option_edit = 'transfersall';
	protected $_order = 't.rec_date';
	protected $_dir = '1';
	protected $_space = 'transfersall.display';

	function display( $tmpl = null )
	{
		/* @var $model transfersModel */
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
