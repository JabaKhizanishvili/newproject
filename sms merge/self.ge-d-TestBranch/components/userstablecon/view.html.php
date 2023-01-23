<?php

class userstableconView extends View
{
	protected $_option = 'userstablecon';
	protected $_option_edit = 'userstableconedit';
	protected $_order = 'w.firstname';
	protected $_dir = '1';
	protected $_space = 'userstables.display';

	function display( $tmpl = null )
	{
		/* @var $model userstableconModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$data = array();
		switch ( $task )
		{
			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;

			default:
				$data = $model->getList();
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
