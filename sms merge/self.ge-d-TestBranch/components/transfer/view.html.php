<?php

class transferView extends View
{
	protected $_option = 'transfers';
	protected $_option_edit = 'transfer';

	function display( $tmpl = null )
	{
		/* @var $model transferModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', 'add' );
		$this->CheckTaskPermision( $task, $this->_option );
		$data = array();
		switch ( $task )
		{
			case 'edit':
				$data = $model->CheckAccess( 'REC_USER' );
				$link = '?option=' . $this->_option;
				if ( !$data )
				{
					Users::Redirect( $link );
				}
				$this->Edit = true;
				break;
			case 'add':
				$data = Request::getVar( 'params', array() );
				$FromDef = Request::getVar( 'fromdef', 0 );
				$ORGID = C::_( 'ORG', $data, 0 );
				if ( empty( $ORGID ) && !empty( $FromDef ) )
				{
					XError::setError( 'data_incorrect' );
					break;
				}
//				$tmpl = 'next';
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
			case 'approve':
				$data = $model->CheckAccess( 'CHIEF' );
				$link = '?option=' . $this->_option;
				if ( !$data )
				{
					Users::Redirect( $link );
				}
				$data->ORG_PLACE_DEF = C::_('ORG_PLACE',XGraph::GetOrgUser(C::_('CHIEF',$data)));
				$tmpl = 'confirm';
				break;
			case 'approvedone':
				$data = Request::getVar( 'params', array() );
		           $ID = C::_( 'ID', $data );
				$status = $model->Approve();
				$link = '?option=' . $this->_option;
				if ( $status )
				{
					XError::setMessage( 'Transfer Approved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
//				$link = '?option=' . $this->_option_edit.'&task=approve&nid[]='.$ID;
//				Users::Redirect( $link );
				$tmpl = 'confirm';
				break;

			case 'delete':
				$link = '?option=' . $this->_option;
				$data = Request::getVar( 'nid', array() );
				$check = $model->CheckAccess( 'CHIEF' );
				if ( empty( $check ) )
				{
					XError::setError( 'Data_Not_Deleted!' );
					Users::Redirect( $link );
					die;
				}
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
