<?php

class r_hrbaseView extends View
{
	protected $_option = 'r_hrbase';
	protected $_option_edit = 'r_hrbase';
	protected $_order = 'p.lastname';
	protected $_dir = '1';
	protected $_space = 'r_hrbase.display';

	function display( $tmpl = null )
	{
		/* @var $model r_hrbaseModel */
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
