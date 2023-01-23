<?php

class hrtableView extends View
{
	protected $_option = 'hrtables';
	protected $_option_edit = 'hrtable';

	function display( $tmpl = null )
	{
		/* @var $model hrtableModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', 'add' );
		$this->CheckTaskPermision( $task, $this->_option );
		$data = array();
		$link = '?option=' . $this->_option;
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
					XError::setMessage( 'Approved!' );
					Users::Redirect( $link );
				}
				else
				{
					XError::setError( 'Not Approved!' );
					Users::Redirect( $link );
				}
				break;
			case 'generate':
				$data = array();
				$ORG = (int) trim( Request::getState( $this->_option_edit, 'org', '-1' ) );
				$BillID = (int) trim( Request::getState( $this->_option_edit, 'BILL_ID', '-1' ) );
				$data['BILL_ID'] = $BillID;
				$data['ORG'] = $ORG;
				$IDx = Request::getVar( 'nid', array() );
				$data['IDX'] = implode( ',', helper::CleanArray( $IDx ) );
				$data['IDS'] = $data['IDX'];
				$tmpl = 'generate';
				break;
			case 'do':
				$data = Request::getVar( 'params', array() );
				if ( $model->Generate( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				$data['IDX'] = $data['IDS'];
				XError::setError( 'data_incorrect' );
				$tmpl = 'generate';
				break;
			case 'send':
				$data = array();
				$link = '?option=' . $this->_option;
				$BillID = (int) trim( Request::getState( $this->_option_edit, 'BILL_ID', '-1' ) );
				$ORG = (int) trim( Request::getState( $this->_option_edit, 'org', '-1' ) );
				$data['BILL_ID'] = $BillID;
				$data['ORG'] = $ORG;
				$IDx = Request::getVar( 'nid', array() );

				if ( empty( $ORG ) )
				{
					if ( $model->multiSend( $data, helper::CleanArray( $IDx ) ) )
					{
						XError::setMessage( 'Data Saved!' );
						Users::Redirect( $link );
					}
				}
				else
				{
					if ( $model->Send( $data, helper::CleanArray( $IDx ) ) )
					{
						XError::setMessage( 'Data Saved!' );
						Users::Redirect( $link );
					}
				}

				XError::setError( 'data already sent!' );
				Users::Redirect( $link );
				break;

			case 'delete':
				$link = '?option=' . $this->_option;
				$data = Request::getVar( 'nid', array() );
				if ( empty( $data ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}
				if ( $model->Delete( $data, 'delete' ) )
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
				$BillID = Request::getInt( 'BILL_ID', 0 );
				$ORG = Request::getInt( 'ORG', 0 );
				if ( empty( $BillID ) )
				{
					XError::setMessage( 'Bill ID Not Defined!' );
					Users::Redirect( $link );
				}
				$data = $model->getItem( $BillID, $ORG );
				if ( C::_( 'STATUS', $data, 0 ) > 1 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'Already Approved!' );
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
