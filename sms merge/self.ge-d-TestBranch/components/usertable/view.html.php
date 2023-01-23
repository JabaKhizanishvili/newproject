<?php

class usertableView extends View
{
	protected $_option = 'usertables';
	protected $_option_edit = 'usertable';

	function display( $tmpl = null )
	{
		/* @var $model usertableModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', 'add' );
		$this->CheckTaskPermision( $task, $this->_option );
		$data = array();
		$Worker = Users::getUser();
		if ( C::_( 'CALCULUS_TYPE', $Worker ) != 2 )
		{
//			$link = '?option=' . $this->_option;
//			XError::setMessage( 'Access Denied!' );
//			Users::Redirect( $link );
		}
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

			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			default:
				$BillID = Request::getInt( 'BILL_ID', 0 );

				if ( empty( $BillID ) )
				{
					$BillID = Helper::GetCurrentBillID( Users::GetUserID() );
				}
				$data = $model->getItem( $BillID );
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
