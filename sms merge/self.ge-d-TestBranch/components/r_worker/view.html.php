<?php

class r_workerView extends View
{
	protected $_option = 'r_worker';
	protected $_option_edit = 'r_worker';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'r_worker.display';

	function display( $tmpl = null )
	{
		/* @var $model r_workersModel */
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
