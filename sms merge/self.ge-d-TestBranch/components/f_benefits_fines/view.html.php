<?php

class f_benefits_finesView extends View
{
	protected $_option = 'f_benefits_fines';
	protected $_option_edit = 'f_benefits_fines';
	protected $_order = 't.id';
	protected $_dir = '0';
	protected $_space = 'f_benefits_fines.display';

	/* @var $model f_benefits_finesModel */
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
