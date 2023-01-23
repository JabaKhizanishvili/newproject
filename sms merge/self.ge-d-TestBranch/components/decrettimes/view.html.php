<?php

class DecretTimesView extends View
{
	protected $_option = 'decrettimes';
	protected $_option_edit = 'decrettime';
	protected $_order = 't.status';
	protected $_dir = '0';
	protected $_space = 'decrettimes.display';

	function display( $tmpl = null )
	{
		/* @var $model DecretTimeSModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
