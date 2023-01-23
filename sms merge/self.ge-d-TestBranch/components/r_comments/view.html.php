<?php

class r_commentsView extends View
{
	protected $_option = 'r_comments';
	protected $_option_edit = 'r_comments';
	protected $_order = 't.id';
	protected $_dir = '1';
	protected $_space = 'r_comments.display';

	function display( $tmpl = null )
	{
		/* @var $model r_commentsModel */
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
