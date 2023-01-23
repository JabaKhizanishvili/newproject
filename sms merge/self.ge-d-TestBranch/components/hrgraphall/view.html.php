<?php

class hrgraphallView extends View
{
	protected $_option = 'hrgraphall';
	protected $_option_edit = 'hrgraphall';
	protected $_order = 'p.lastname';
	protected $_dir = '0';
	protected $_space = 'hrgraphall.display';

	function display( $tmpl = null )
	{
		/* @var $model GraphsModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		$Data = array();
		switch ( $task )
		{
			case 'copydata':
				$Data = $model->getList();
				$CopyData = C::_( 'items', $Data, array() );
				$Group = (int) trim( Request::getVar( 'group_id', 0 ) );
				if ( empty( $Group ) )
				{
					XError::setError( 'data_incorrect' );
					$link = '?option=' . $this->_option;
					Users::Redirect( $link );
				}
				if ( $model->CopyData( $CopyData, $Group ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );

				break;
			default:
				$Data = $model->getList();
				break;
		}
		if ( !is_object( $Data ) )
		{
			$Data = (object) $Data;
		}
		$this->assignRef( 'data', $Data );
		parent::display( $tmpl );

	}

}
