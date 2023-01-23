<?php

class PersonsView extends View
{
	protected $_option = 'persons';
	protected $_option_edit = 'person';
	protected $_order = 't.change_date';
	protected $_dir = '1';
	protected $_space = 'persons.display';

	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
