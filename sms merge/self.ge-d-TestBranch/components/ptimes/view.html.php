<?php

class PTimesView extends View
{
	protected $_option = 'ptimes';
	protected $_option_edit = 'ptime';
	protected $_order = 't.start_date';
	protected $_dir = '1';
	protected $_space = 'ptimes.display';

	function display( $tmpl = null )
	{
		/* @var $model PTimesModel */
		if ( !Helper::getConfig( 'private_date' ) )
		{
			XError::setError( 'private time Is disabled' );
			Users::Redirect();
		}
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
