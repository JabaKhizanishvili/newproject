<?php

class Job_descriptionsView extends View
{
	protected $_option = 'job_descriptions';
	protected $_option_edit = 'job_description';
	protected $_order = 'ordering';
	protected $_dir = '0';
	protected $_space = 'job_descriptions.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
