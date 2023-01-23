<?php

class ProfileView extends View
{
	protected $_option = 'profile';
	protected $_option_edit = 'profileedit';
	protected $_space = 'Profile.display';

	function display( $tpl = null )
	{
		/* @var $model ProfileModel */
		$model = $this->getModel();
		$WorkerData = $model->getWorker();
//		$WorkerOrgData = $model->getWorkerOrgData();
		$this->assignRef( 'workerdata', $WorkerData );
//		$this->assignRef( 'workerorgdata', $WorkerOrgData );
		$this->assignRef( 'model', $model );
		$layout = Request::getCmd( 'layout', '' );
		$task = Request::getVar( 'task', '' );
		$data = [];
		switch ( $layout )
		{
			case 'sessions':
				$tpl = 'sessions';
				$data = $model->getSessions();
				$this->assignRef( 'data', $data );
				break;
		}
		$link = '?option=' . $this->_option.'&layout=sessions';
		switch ( $task )
		{
            case 'keepAlive':
                echo 'okay';
                die();
                break;
			case 'delete':
				$data = Request::getVar( 'nid', array() );
				if ( empty( $data ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}
				if ( $model->DeleteSession( $data ) )
				{
					XError::setMessage( 'Data Deleted!' );
					Users::Redirect( $link );
				}
				XError::setError( 'Data_Not_Deleted!' );
				Users::Redirect( $link );
				break;
			case 'deleteAll':
				if ( $model->DeleteAllSession() )
				{
					XError::setMessage( 'Data Deleted!' );
					Users::Redirect( $link );
				}
				XError::setError( 'Data_Not_Deleted!' );
				Users::Redirect( $link );
				break;
		}

		parent::display( $tpl );

	}

}
