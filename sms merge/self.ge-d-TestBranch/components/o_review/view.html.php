<?php

class o_reviewView extends View
{
	protected $_option = 'o_review';
	protected $_option_edit = 'o_review';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'o_review.display';

	function display( $tmpl = null )
	{
		/* @var $model o_reviewsModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		if ( $task == 'save' )
		{
			$data = Request::getVar( 'params', array() );
			$model->SaveData( $data );
		}
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
