<?php

class hlimitsView extends View
{
	protected $_option = 'hlimits';
	protected $_option_edit = 'hlimit';
	protected $_order = 'w.lastname';
	protected $_dir = '0';
	protected $_space = 'hlimits.display';

	function display( $tmpl = null )
	{
		/* @var $model hlimitsModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		switch ( $task )
		{
			case 'export':
				$link = '?option=' . $this->_option;
				if ( !Helper::CheckTaskPermision( 'export', $this->_option ) )
				{
					XError::setError( 'you cannot access task' );
					Users::Redirect( $link );
				}

				if ( $model->Export() )
				{
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;
		}
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
