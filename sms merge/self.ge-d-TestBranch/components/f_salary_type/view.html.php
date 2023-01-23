<?php

class F_salary_typeView extends View
{
	protected $_option = 'f_salary_types';
	protected $_option_edit = 'f_salary_type';

	function display( $tmpl = null )
	{
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

			case 'copydata':
				$data = $model->getItem();
				if ( !$data )
				{
					$link = '?option=' . $this->_option;
					Users::Redirect( $link );
				}
				$data->ID = '';
				break;

			case 'apply':
				$data = Request::getVar( 'params', array() );
				$ID = $model->SaveData( $data );
				if ( $ID )
				{
					$link = '?option=' . $this->_option_edit . '&task=edit&nid[]=' . $ID;
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

				$exist = Xhelp::CheckWorkersInOrg( $in, 'SALARYTYPE' );
				$data = array_diff( $in, $exist );
				if ( count( $exist ) && !count( $data ) )
				{
					XError::setError( 'workers detected in this id!' );
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

			case 'changestate':
				$link = '?option=' . $this->_option;
				$data = $model->getItem();
				if ( C::_( 'ACTIVE', $data ) == 1 )
				{
					$exist = Xhelp::CheckWorkersInOrg( C::_( 'ID', $data ), 'SALARYTYPE' );
					if ( count( $exist ) )
					{
						XError::setError( 'workers detected in this id!' );
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