<?php

class overtimeworkersView extends View
{
	protected $_option = 'overtimeworkers';
	protected $_option_edit = 'overtimeworker';
	protected $_order = 't.start_date';
	protected $_dir = '1';
	protected $_space = 'overtimeworkers.display';

	function display( $tmpl = null )
	{
		/* @var $model overtimeworkersModel */
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
