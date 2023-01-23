<?php

class r_checklistView extends View
{
	protected $_option = 'r_checklist';
	protected $_option_edit = 'r_checklist';
	protected $_order = 'dum.lastname';
	protected $_dir = '1';
	protected $_space = 'r_checklist.display';

	function display( $tmpl = null )
	{
		/* @var $model r_rsModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		

		
			
		switch ( $task )
		{
			case 'export':
				if ( $model->E_xport() )
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
