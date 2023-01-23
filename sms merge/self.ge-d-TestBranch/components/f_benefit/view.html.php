<?php

class f_benefitView extends View
{
	protected $_option = 'f_benefits';
	protected $_option_edit = 'f_benefit';

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

				$exists = $model->check_actives( $in );
				$data = array_diff( $in, $exists );
				if ( count( $exists ) && !count( $data ) )
				{
					XError::setError( 'limited access record!' );
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
					$id = C::_( 'ID', $data );
					$exists = $model->check_actives( $id );
					if ( in_array( $id, $exists ) )
					{
						XError::setError( 'limited access record!' );
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
