<?php

class ProfileEditView extends View
{
	protected $_option = 'profile';
	protected $_option_edit = 'profileedit';

	function display( $tmpl = null )
	{
		/* @var $model ProfileEditModel */
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
			case 'login':
				$link = '?option=' . $this->_option;
				if ( $model->Login() )
				{
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				Users::Redirect( $link );
				break;
			case 'logout':
				$link = '?option=' . $this->_option;
				if ( $model->LogOut() )
				{
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				Users::Redirect( $link );
				break;
			case 'gpslogin':
				$link = '?option=' . $this->_option;
				if ( $model->GPSLogin() )
				{
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				Users::Redirect( $link );
				break;
			case 'gpslogout':
				$link = '?option=' . $this->_option;
				if ( $model->GPSLogOut() )
				{
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				Users::Redirect( $link );
				break;
			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			default:
				$data = $model->getItem();
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
