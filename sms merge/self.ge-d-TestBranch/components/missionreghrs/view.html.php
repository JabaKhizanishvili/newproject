<?php

class MissionRegHRsView extends View
{
	protected $_option = 'missionreghrs';
	protected $_option_edit = 'missionreghr';
	protected $_order = 't.status';
	protected $_dir = '0';
	protected $_space = 'missionregdisplay';

	function display( $tmpl = null )
	{
		/* @var $model MissionRegHRsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
