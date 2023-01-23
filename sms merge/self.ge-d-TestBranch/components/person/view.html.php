<?php

class PersonView extends View
{
	protected $_option = 'persons';
	protected $_option_assign = 'person_org';
	protected $_option_edit = 'person';

	function display( $tmpl = null )
	{
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', 'add' );
		$this->CheckTaskPermision( $task, $this->_option );
		$data = array();
		switch ( $task )
		{
			case 'passwordresset':
				$data = Request::getVar( 'nid', array() );
				$link = '?option=' . $this->_option;
				if ( empty( $data ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}
				if ( $model->PasswordReset( $data ) )
				{
					XError::setMessage( 'AUTH DATA SENT SUCCESSFULLY' );
					Users::Redirect( $link );
				}
				else
				{
					Users::Redirect( $link );
				}
				break;

			case 'unset':
				$link = '?option=' . $this->_option;
				$in = Request::getVar( 'nid', array() );
				if ( empty( $in ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}

				$exist = Xhelp::CheckWorkersInOrg( $in, 'PERSON' );
				$data = array_diff( $in, $exist );
				if ( count( $exist ) && !count( $data ) )
				{
					if ( count( $exist ) )
					{
						XError::setError( 'persons detected in this org!' );
						Users::Redirect( $link );
					}
				}

				if ( $model->UnsetUser( $data ) )
				{
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}

				XError::setError( 'data_incorrect' );
				Users::Redirect( $link );
				break;

			case 'assignment':
				$data = Request::getVar( 'nid', array() );
				$ids = implode( ',', $data );
				$link = '?option=person_org&task=&params[PERSON]=' . $ids;
				if ( !Xhelp::checkPersonsActive( $ids ) )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'select active persons!' );
				}
				Users::Redirect( $link );
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
            case 'save_and_assign':
                $data = Request::getVar( 'params', array() );
                if ( $personId = $model->SaveData( $data ) ) {
                    $link = '?option=' . $this->_option_assign . '&task=save_and_assign&params[PERSON]=' . $personId;
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

				$exist = Xhelp::CheckWorkersInOrg( $in, 'PERSON' );
				$data = array_diff( $in, $exist );
				if ( count( $exist ) && !count( $data ) )
				{
					if ( count( $exist ) )
					{
						XError::setError( 'persons detected in this org!' );
						Users::Redirect( $link );
					}
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

			case 'changestate':
				$model->ChangeState();
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Status Changed!' );
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
