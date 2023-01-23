<?php

class MissionRegHRView extends View
{
	protected $_option = 'missionreghrs';
	protected $_option_edit = 'missionreghr';

	function display( $tmpl = null )
	{
		/* @var $model MissionRegHRModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', 'add' );
		$this->CheckTaskPermision( $task, $this->_option );
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
			case 'approve':
				$status = $model->Approve();
				$link = '?option=' . $this->_option;
				if ( $status )
				{
					XError::setMessage( 'Mission Approved!' );
					Users::Redirect( $link );
				}
				else
				{
					XError::setError( 'Mission Not Approved!' );
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

			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			default:
				$data = $model->getItem();
				if ( C::_( 'STATUS', $data, 0 ) != 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'Mission Already Approved!' );
//                    Users::Redirect($link);
				}
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
