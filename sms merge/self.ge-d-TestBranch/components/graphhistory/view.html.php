<?php

class GraphHistoryView extends View
{
	protected $_option = 'graphhistory';
	protected $_option_edit = 'graphhistory';
	protected $_order = 'a.hist_start_date';
	protected $_dir = '1';

//	protected $_space = 'workersrests.display';

	function display( $tmpl = null )
	{
		/* @var $model GraphHistoryModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
