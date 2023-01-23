<?php

class pmissionsAsWorkersView extends View
{
	protected $_option = 'pmissionsasworkers';
	protected $_option_edit = 'pmissionsasworker';
	protected $_order = 't.start_date';
	protected $_dir = '1';
	protected $_space = 'pmissionsasworkers.display';

	function display( $tmpl = null )
	{
		/* @var $model pmissionsAsWorkersModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		switch ( $task )
		{
			case 'export':
				$tmpl = 'export';
				if ( Helper::getConfig( 'apps_mission_add_type', 0 ) == 1 )
				{
					$tmpl = 'export_info';
				}

				if ( $model->Export( $tmpl ) )
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
