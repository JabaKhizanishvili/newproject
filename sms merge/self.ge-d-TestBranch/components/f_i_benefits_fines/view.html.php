<?php

class f_i_benefits_finesView extends View
{
    protected $_option = 'f_i_benefits_fines';
    protected $_option_edit = 'f_i_benefit_fine';
    protected $_order = 't.id';
    protected $_dir = '0';
    protected $_space = 'f_i_benefits_fines.display';

    function display($tmpl = null)
    {
        /* @var $model f_i_benefits_finesModel */
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
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
