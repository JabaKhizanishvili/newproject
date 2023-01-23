<?php

class BulletinView extends View
{
	protected $_option = 'bulletins';
	protected $_option_edit = 'bulletin';

	function display( $tmpl = null )
	{
		/* @var $model BulletinModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', 'add' );
		$this->CheckTaskPermision( $task, $this->_option );
		$data = array();
		switch ( $task )
		{
			case 'additional_status':
				$data = Request::getVar( 'nid', array() );
				if ( $model->Additional_Status( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;
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

			case 'continue':
				$status = $model->BContinue();
				$link = '?option=' . $this->_option;
				if ( $status )
				{
					XError::setMessage( 'Bulletins Approved!' );
					Users::Redirect( $link );
				}
				else
				{
					XError::setError( 'Bulletins Not Approved!' );
					Users::Redirect( $link );
				}
				break;

			case 'delete':
				$link = '?option=' . $this->_option;
				$data = Request::getVar( 'nid', array() );
				if ( empty( $data ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}
				if ( $model->Delete( $data ) )
				{
					XError::setMessage( 'Data Deleted!' );
					Users::Redirect( $link );
				}
				XError::setError( 'Data_Not_Deleted!' );
				Users::Redirect( $link );
				break;

			case 'confirm':
				$data = $model->getItem();
				$ID = C::_( 'ID', $data );
				if ( $ID )
				{
					$data->STATUS = 3;
				}
				break;

			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			default:
				$data = $model->getItem();
				if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) && C::_( 'STATUS', $data, 0 ) == 3 )
				{
					$link = '?option=' . $this->_option;
					Error::setError( 'Bulletins Already Approved!' );
					Users::Redirect( $link );
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
