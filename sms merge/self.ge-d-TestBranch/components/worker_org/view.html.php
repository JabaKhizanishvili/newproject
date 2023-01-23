<?php

class Worker_orgView extends View
{
	protected $_option = 'workers_orgs';
	protected $_option_edit = 'worker_org';

	function display( $tmpl = null )
	{
		/* @var $model WorkerModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$data = array();
		$ORGdata = array();
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

			case 'release':
				$ORG = (int) trim( Request::getState( 'workers.display', 'org', '' ) );
				if ( !empty( $ORG ) )
				{
					$data = Request::getVar( 'nid', array() );
					$tmpl = 'release';
				}
				else
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'PLEASE, CHOOSE ORG!' );
					Users::Redirect( $link );
				}
				break;

			case 'changing':
				$tmpl = 'changing';
				break;

			case 'assignment':
				$tmpl = 'assignment';
				break;

			case 'changerole':
				$tmpl = 'role';
				break;

			case 'changecategory':
				$tmpl = 'category';
				break;

			case 'unset':
				$link = '?option=' . $this->_option;
				$data = Request::getVar( 'nid', array() );
				if ( empty( $data ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}
				if ( $model->UnsetUser( $data ) )
				{
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				Users::Redirect( $link );
				break;
				
			case 'saverole':
				$data = Request::getVar( 'params', array() );
				$tmpl = 'role';
				if ( $model->SaveUserRole( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;
			case 'savecategory':
				$data = Request::getVar( 'params', array() );
				$tmpl = 'category';
				if ( $model->SaveCategory( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;
			case 'passwordresset':
				$data = Request::getVar( 'nid', array() );
				if ( empty( $data ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}
				$link = '?option=' . $this->_option;
				if ( $model->PasswordReset( $data ) )
				{
					XError::setMessage( 'AUTH DATA SENT SUCCESSFULLY' );
					Users::Redirect( $link );
				}
				else
				{
//					XError::setError( 'AUTH DATA Not SENT SUCCESSFULLY' );
					Users::Redirect( $link );
				}
				break;

			case 'changegraph':
				$tmpl = 'graph';
				break;

			case 'savegraph':
				$data = Request::getVar( 'params', array() );
				$tmpl = 'graph';
				if ( $model->SaveUserGraph( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
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

			case 'fulldelete':
				$link = '?option=' . $this->_option;
				$data = Request::getVar( 'params', array() );
				$ID = C::_( 'ID', $data );
				if ( empty( $ID ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}
				if ( $model->Delete( (array) $ID, 'Delete' ) )
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
				$ORGdata = $model->getOrgData();
				break;
		}
		if ( !is_object( $data ) )
		{
			$data = (object) $data;
		}
		$this->assignRef( 'data', $data );
		$this->assignRef( 'orgdata', $ORGdata );
		parent::display( $tmpl );

	}

	public function getValue( $Key, $SalaryData )
	{
		$Value = trim( C::_( $Key, $SalaryData ) );
		if ( mb_strtolower( $Value ) == 'null' )
		{
			$Value = null;
		}
		return $Value;

	}

}
