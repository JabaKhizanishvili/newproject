<?php

class r_doorsView extends View
{
	protected $_option = 'r_doors';
	protected $_option_edit = 'r_doors';
	protected $_order = 't.rec_date';
	protected $_dir = '0';
	protected $_space = 'r_doors.display';

	function display( $tmpl = null )
	{
		/* @var $model r_doorsModel */
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
