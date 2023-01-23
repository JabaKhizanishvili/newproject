<?php

class r_workedtimesView extends View
{
	protected $_option = 'r_workedtimes';
	protected $_option_edit = 'r_workedtime';
	protected $_order = 'wt.start_date';
	protected $_dir = '1';
	protected $_space = 'r_workedtimes.display';

	function display( $tmpl = null )
	{
		/* @var $model r_workedtimesModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
//		$this->CheckTaskPermision( $task, $this->_option );
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
