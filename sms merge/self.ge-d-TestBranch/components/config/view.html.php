<?php

class ConfigView extends View
{
	protected $_option = 'configs';
	protected $_option_edit = 'config';

	function display( $tmpl = null )
	{
		/* @var $model ConfigModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$C = Request::getCMD( 'c', 'config' );
		if ( $C != 'config' )
		{
			$this->assignRef( 'menu', $Menu );
		}
		$Modal = Request::getCMD( 'tmpl', '' );
		$data = array();
		switch ( $task )
		{
			case 'save':
				$data = Request::getVar( 'params', array() );
				if ( $model->SaveData( $data ) )
				{
					if ( $C && $Modal )
					{
						Helper::CloseModal( null, true );
					}
					else
					{
						XError::setMessage( 'Data Saved!' );
						$link = '?option=' . $this->_option;
						Users::Redirect( $link );
					}
					break;
				}
				XError::setError( 'data_incorrect' );
				break;

			case 'cancel':
				if ( $C && $Modal )
				{
					Helper::CloseModal( null, true );
				}
				else
				{
					XError::setInfo( 'action canceled!' );
					$link = '?option=' . $this->_option;
					Users::Redirect( $link );
				}
				break;

			case 'apply':
				$data = Request::getVar( 'params', array() );
				$ID = $model->SaveData( $data );
				if ( $ID )
				{
					if ( $C )
					{
						$link = '?option=' . $this->_option_edit . '&task=edit&c=' . $C . '&tmpl=' . $Modal;
					}
					else
					{
						$link = '?option=' . $this->_option_edit . '&task=edit';
					}
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;
			default:
				$data = $model->getItems( $C );
				break;
		}
		$this->assignRef( 'data', $data );
		$this->assignRef( 'C', $C );
		parent::display( $tmpl );

	}

}
