<?php

class WOfficialsView extends View
{
	protected $_option = 'wofficials';
	protected $_option_edit = 'wofficials';
	protected $_order = 't.start_date';
	protected $_dir = '1';
	protected $_space = 'wofficials.display';

	function display( $tmpl = null )
	{
		/* @var $model WOfficialSModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
