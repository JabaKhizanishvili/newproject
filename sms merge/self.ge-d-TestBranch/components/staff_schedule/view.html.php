<?php

class Staff_scheduleView extends View
{
	protected $_option = 'staff_schedules';
	protected $_option_edit = 'staff_schedule';

	function display( $tmpl = null )
	{
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$ORG = (int) trim( Request::getState( 'staff_schedules.display', 'org', '' ) );
		if ( empty( $ORG ) )
		{
			$link = '?option=' . $this->_option;
			XError::setError( 'PLEASE, CHOOSE ORG!' );
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

			case 'delete':
				$link = '?option=' . $this->_option;
				$in = Request::getVar( 'nid', array() );
				if ( empty( $in ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}

				$exist = Xhelp::CheckWorkersInOrg( $in, 'STAFF_SCHEDULE' );
				$data = array_diff( $in, $exist );
				if ( count( $exist ) && !count( $data ) )
				{
					XError::setError( 'unit cannot be deleted which is used in other operations!' );
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
			case 'copy':
				/** @var Job_descriptionTable $data */
				$data = $model->getItem();
				$key = $data->getKeyName();
				$data->{$key} = null;
				break;

			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			case 'changestate':
				$link = '?option=' . $this->_option;
				$data = $model->getItem();
				if ( C::_( 'ACTIVE', $data ) == 1 )
				{
					$exist = Xhelp::CheckWorkersInOrg( C::_( 'ID', $data ), 'STAFF_SCHEDULE' );
					if ( count( $exist ) )
					{
						XError::setError( 'workers detected on staff_schedule!' );
						Users::Redirect( $link );
					}
				}
				else
				{
					XError::setMessage( 'Status Changed!' );
				}
				$model->ChangeState();
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
