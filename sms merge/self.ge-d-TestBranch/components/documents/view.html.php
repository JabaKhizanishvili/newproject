<?php

class DocumentsView extends View
{
	protected $_option = 'documents';
	protected $_option_edit = 'document';
	protected $_order = 't.lib_title';
	protected $_dir = '0';
	protected $_space = 'documents.display';

	function display( $tmpl = null )
	{
		/* @var $model VisitorsModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
