<?php

class wmissionsasworkersView extends View
{
	protected $_option = 'wmissionsasworkers';
	protected $_option_edit = 'wmissionsasworker';
	protected $_order = 't.rec_date';
	protected $_dir = '1';
	protected $_space = 'wmissionsasworkers.display';

	function display( $tmpl = null )
	{
		/* @var $model pmissionsAsWorkersModel */
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
