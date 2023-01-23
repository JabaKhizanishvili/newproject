<?php

class ConfigsView extends View
{
	protected $_option = 'configs';
	protected $_option_edit = 'config';

	function display( $tmpl = null )
	{
		/* @var $model DecretsModel */
		$model = $this->getModel();
		$Task = Request::getVar( 'task', '' );
		switch ( $Task )
		{
			case 'edit':
				$Option = C::_( '0', Request::getVar( 'nid' ) );
				$link = '?option=hist&amp;c=' . $Option . '&amp;task=' . $Task;
				Users::Redirect( $link );
				break;
		}
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
