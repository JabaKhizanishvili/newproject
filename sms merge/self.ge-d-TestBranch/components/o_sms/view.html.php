<?php

class o_smsView extends View
{
	protected $_option = 'live';
	protected $_option_edit = 'o_sms';

	function display( $tmpl = null )
	{
		/* @var $model o_smsModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$ID = (int) Request::getInt( 'worker', 0 );
		if ( !$ID )
		{
			$link = '?option=' . $this->_option;
			XError::setMessage( 'data_incorrect' );
			Users::Redirect( $link );
		}
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
//	
//			case 'delete':
//				$link = '?option=' . $this->_option;
//				$data = Request::getVar( 'nid', array() );
//				if ( empty( $data ) )
//				{
//					Error::setError( 'items_not_selected' );
//					Users::Redirect( $link );
//				}
//				if ( $model->Delete( $data ) )
//				{
//					Error::setMessage( 'Data Deleted!' );
//					Users::Redirect( $link );
//				}
//				Error::setError( 'Data_Not_Deleted!' );
//				Users::Redirect( $link );
//				break;

			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			default:
				$data = $model->getItem( $ID );
				break;
		}
		if ( !is_object( $data ) )
		{
			$data = (object) $data;
		}
		$this->assignRef( 'data', $data );
		$this->assignRef( 'ID', $ID );
		parent::display( $tmpl );

	}

}
