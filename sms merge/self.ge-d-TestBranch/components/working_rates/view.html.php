<?php

class Working_ratesView extends View
{
	protected $_option = 'working_rates';
	protected $_option_edit = 'working_rate';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'working_rates.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
