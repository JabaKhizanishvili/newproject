<?php

class TranslationsView extends View
{
	protected $_option = 'translations';
	protected $_option_edit = 'translation';
	protected $_order = 'lib_from';
	protected $_dir = '0';
	protected $_space = 'translations.display';

	function display( $tmpl = null )
	{
		/* @var $model TranslationsModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );

		$data = null;
		switch ( $task )
		{
			case 'start_new':
				$tmpl = 'new';
				$set = Request::getVar( 'set' );
				$data = $model->getSearch( $set );
				break;

			case 'go_back':
				break;
		}

		if ( is_null( $data ) )
		{
			$data = $model->getList();
		}

		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
