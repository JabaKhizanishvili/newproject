<?php

class bulletinasuserView extends View
{
	protected $_option = 'bulletinasusers';
	protected $_option_edit = 'bulletinasuser';

	function display( $tmpl = null )
	{
		/* @var $model bulletinasuserModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', 'add' );
		$this->CheckTaskPermision( $task, $this->_option );
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
				
			case 'continue':
				$status = $model->BContinue();
				$link = '?option=' . $this->_option;
				if ( $status )
				{
					XError::setMessage( 'Bulletins Approved!' );
					Users::Redirect( $link );
				}
				else
				{
					XError::setError( 'Bulletins Not Approved!' );
					Users::Redirect( $link );
				}
				break;

			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			default:
				$data = array(); // $model->getItem();
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
