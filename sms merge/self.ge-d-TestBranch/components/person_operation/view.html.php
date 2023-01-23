<?php

class Person_operationView extends View
{
	protected $_option = 'person_operations';
	protected $_option_edit = 'person_operation';

	function display( $tmpl = null )
	{
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

			case 'stopprocess':
				$link = '?option=' . $this->_option;
				$data = Request::getVar( 'nid', array() );
				if ( empty( $data ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}
				if ( $model->StopProcess( $data ) )
				{
					XError::setMessage( 'process has stopped!' );
					Users::Redirect( $link );
				}
				XError::setError( 'process has not stopped!' );
				Users::Redirect( $link );
				break;

			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			case 'changestate':
				$model->ChangeState();
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Status Changed!' );
				Users::Redirect( $link );
				break;

			default:
				$data = $model->getItem();
				if ( C::_( 'STATUS', $data ) > 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'You can not edit complited operations!' );
					Users::Redirect( $link );
				}
				$type = C::_( 'CHANGE_TYPE', $data );
				if ( $type == 3 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'You can not edit this type of operation!' );
					Users::Redirect( $link );
				}
				if ( $type == 7 )
				{
					$token = C::_( 'TOKEN', $data );
					$data = $model->getItem( $token, 'TOKEN', 5 );
				}
				break;
		}
		if ( !is_object( $data ) )
		{
			$data = (object) $data;
		}
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
