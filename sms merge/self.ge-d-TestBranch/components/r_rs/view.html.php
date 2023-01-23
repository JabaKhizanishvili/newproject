<?php

class r_rsView extends View
{
	protected $_option = 'r_rs';
	protected $_option_edit = 'r_rs';
	protected $_order = 'w.lastname';
	protected $_dir = '1';
	protected $_space = 'r_rs.display';

	function display( $tmpl = null )
	{
		/* @var $model r_rsModel */
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
