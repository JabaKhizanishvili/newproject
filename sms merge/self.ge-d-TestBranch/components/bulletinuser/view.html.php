<?php

class bulletinuserView extends View
{
	protected $_option = 'bulletinusers';
	protected $_option_edit = 'bulletinuser';

	function display( $tmpl = null )
	{
		/* @var $model bulletinuserModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', 'add' );
		$this->CheckTaskPermision( $task, $this->_option );
		$data = array();
		switch ( $task )
		{
			case 'upload_bulletin':
				$data = $model->getdata();
				if ( C::_( 'STATUS', $data ) != 2 )
				{
					XError::setError( 'You can not access to this type of bulletin!' );
					$link = '?option=' . $this->_option;
					Users::Redirect( $link );
				}
				$tmpl = "upload";
				break;
			case 'uploadbulletin':
				$data = Request::getVar( 'params', array() );
				if ( $model->UploadBulletin( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				$tmpl = "upload";
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
