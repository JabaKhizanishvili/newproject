<?php

class HolidayRegHRView extends View
{
	protected $_option = 'holidayreghrs';
	protected $_option_edit = 'holidayreghr';

	function display( $tmpl = null )
	{
		/* @var $model HolidayRegHRModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', 'add' );
		$this->CheckTaskPermision( $task, $this->_option );
		$data = array();
		switch ( $task )
		{
			case 'cancel_rel':
				$json = Request::getVar( 'json' );
				$data = json_decode( $json );
				break;

			case 'save_rel':
				$data = Request::getVar( 'params', array() );
				$replacers = C::_( 'REPLACING_WORKERS', $data );
				if ( in_array( -1, array_values( $replacers ) ) )
				{
					XError::setError( 'select replacer workers!' );
					$json = Request::getVar( 'json' );
					$data = $model->getDayList( json_decode( $json ) );
					$tmpl = 'days';
					break;
				}

				if ( $model->SaveDataRel( $replacers ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;

			case 'save':
				$data = Request::getVar( 'params', array() );
				$replacers = Helper::CleanArray( (array) explode( ',', C::_( 'REPLACING_WORKERS', $data ) ) );
				$App = TaskHelper::getLimitAppType( C::_( 'TYPE', $data ) );
				$replacers_type = C::_( 'REPLACER_FIELD', $App );

				if ( empty( $replacers ) && $replacers_type == 3 )
				{
					XError::setError( 'data_incorrect' );
					break;
				}

//				if ( empty( $model->CheckData( $data ) ) )
//				{
//					break;
//				}

				if ( count( $replacers ) > 1 && in_array( $replacers_type, [ 3, 4 ] ) )
				{
					$data = $model->getDayList( $data );
					$tmpl = 'days';
					break;
				}

				if ( $model->Save_Data( $data ) )
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
					XError::setMessage( 'Holiday Approved!' );
					Users::Redirect( $link );
				}
				else
				{
					XError::setError( 'Holiday Not Approved!' );
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
				if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) && C::_( 'STATUS', $data, 0 ) != 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'Holiday Already Approved!' );
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
