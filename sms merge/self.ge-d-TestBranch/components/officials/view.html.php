<?php

class OfficialsView extends View
{
	protected $_option = 'officials';
	protected $_option_edit = 'official';
	protected $_order = 't.start_date';
	protected $_dir = '1';
	protected $_space = 'officials.display';

	function display( $tmpl = null )
	{
		/* @var $model OfficialSModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
