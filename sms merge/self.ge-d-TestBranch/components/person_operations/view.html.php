<?php

class Person_operationsView extends View
{
	protected $_option = 'person_operations';
	protected $_option_edit = 'person_operation';
	protected $_order = 't.change_date';
	protected $_dir = '0';
	protected $_space = 'person_operations.display';

	function display( $tmpl = null )
	{
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
