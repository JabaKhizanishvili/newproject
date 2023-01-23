<?php

class o_user_outView extends View
{
	protected $_option = 'o_user_out';
	protected $_option_edit = 'o_user_out';

	function display( $tmpl = null )
	{
		/* @var $model o_user_outModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$data = array();
		switch ( $task )
		{
			case 'save':
				$data = Request::getVar( 'params', array() );				
				if ( $model->SaveData( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;
			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			default:
				break;
		}
		if ( !is_object( $data ) )
		{
			$data = (object) $data;
		}
		if ( empty( $data->WORKER ) )
		{
			$data->WORKER = C::_( 'worker', 'get', null );
		}
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
