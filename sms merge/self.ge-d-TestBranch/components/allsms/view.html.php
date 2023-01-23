<?php

class allsmsView extends View
{
	protected $_option = 'allsms';
	protected $_option_edit = 'allsms';

	function display( $tmpl = null )
	{
		/* @var $model allsmsModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$data = array();
		switch ( $task )
		{
			case 'edit':
				$data = Request::getVar( 'params', array() );
				$tmpl = 'generate';
				break;
			case 'nextstep':
				$data = Request::getVar( 'params', array() );
				if ( $data['ORG'] <= 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'PLEASE, CHOOSE ORG!' );
					Users::Redirect( $link );
				}
				$tmpl = 'generate';
				break;
			case 'next':
				$dataX = Request::getVar( 'params', array() );
				if ( !$model->CheckData( $dataX ) )
				{
					$data = $dataX;
					XError::setError( 'data_incorrect' );
					$tmpl = 'generate';
					break;
				}
				$data = $model->Prepare( $dataX );
				$tmpl = 'confirm';
				break;
			case 'confirm':
				$data = Request::getVar( 'params', array() );
				if ( $model->SendSMS( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'All SMS Sent!' );
					Users::Redirect( $link );
					break;
				}
				XError::setError( 'System Error' );
				break;
			case 'generate':
				$data = Request::getVar( 'params', array() );
				if ( $data['ORG'] <= 0 )
				{
					XError::setError( 'PLEASE, CHOOSE ORG!' );
					$tmpl = null;
				}
				else
				{
					$tmpl = 'generate';
				}
				break;
			case 'cancel':
				$link = '?option=' . $this->_option;
				XError::setMessage( 'action canceled!' );
				Users::Redirect( $link );
				break;

			default:
				$data = Request::getVar( 'params', array() );
				break;
		}
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
