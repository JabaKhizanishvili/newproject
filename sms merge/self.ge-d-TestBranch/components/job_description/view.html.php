<?php

class Job_descriptionView extends View
{
	protected $_option = 'job_descriptions';
	protected $_option_edit = 'job_description';

	function display( $tmpl = null )
	{
		$params = (object) get_object_vars( $this );
		/** @var Job_descriptionModel $model */
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$data = array();
		switch ( $task )
		{
			case 'save':
				$data = Request::getVar( 'params', array() );
				$data['V_FUNCTIONS'] = str_replace( PHP_EOL, '', C::_( 'params.V_FUNCTIONS', Request::get( 'post', 4 ) ) );
				$data['V_TASKS'] = str_replace( PHP_EOL, '', C::_( 'params.V_TASKS', Request::get( 'post', 4 ) ) );
				$data['V_RESPONSIBILITIES'] = str_replace( PHP_EOL, '', C::_( 'params.V_RESPONSIBILITIES', Request::get( 'post', 4 ) ) );
				$data['V_REQUIREMENTS'] = str_replace( PHP_EOL, '', C::_( 'params.V_REQUIREMENTS', Request::get( 'post', 4 ) ) );
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
			case 'changestate':
				$model->ChangeState();
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Status Changed!' );
				Users::Redirect( $link );
				break;
			case 'copy':
				/** @var Job_descriptionTable $data */
				$data = $model->getItem();
				$key = $data->getKeyName();
				$data->{$key} = null;
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
