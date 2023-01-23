<?php

class BulletinsView extends View
{
	protected $_option = 'bulletins';
	protected $_option_edit = 'bulletin';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'bulletins.display';

	function display( $tmpl = null )
	{
		/* @var $model BulletinsModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		switch ( $task )
		{
			case 'export':
				if ( $model->Export() )
				{
					$link = '?option=' . $this->_option;
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
