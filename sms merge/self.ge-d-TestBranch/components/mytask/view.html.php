<?php

class MyTaskView extends View
{
	protected $_option = 'mytasks';
	protected $_option_edit = 'mytask';

	function display( $tmpl = null )
	{
		/* @var $model MyTaskModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		$data = Request::getVar( 'nid', array() );
		$ID = Collection::get( '0', $data );
		switch ( $task )
		{
			case 'approve':
				$status = $model->Approve();
				$link = '?option=' . $this->_option;
				if ( $status )
				{
					XError::setMessage( 'Application Approved!' );
					Users::Redirect( $link );
				}
				else
				{
					XError::setError( 'Application Not Approved!' );
					Users::Redirect( $link );
				}
				break;
			case 'decline':
				$status = $model->Decline();
				$link = '?option=' . $this->_option;
				if ( $status )
				{
					XError::setMessage( 'Application Approved!' );
					Users::Redirect( $link );
				}
				else
				{
					XError::setError( 'Application Not Approved!' );
					Users::Redirect( $link );
				}
				break;
			case 'cancel':
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			default:
				$WData = $model->getTask( $ID );
				break;
		}
		$this->assignRef( 'wdata', $WData );
		TaskHelper::LogTaskView( $WData );
		parent::display( $tmpl );

	}

}
