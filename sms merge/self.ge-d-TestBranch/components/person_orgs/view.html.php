<?php

class Person_orgsView extends View
{
	protected $_option = 'person_orgs';
	protected $_option_edit = 'person_org';
	protected $_order = 'sc.change_date';
	protected $_dir = '0';
	protected $_space = 'person_orgs.display';

	function display( $tmpl = null )
	{
		/* @var $model Person_orgsModel */
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
		$data = $model->getList( false, $tmpl );
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
