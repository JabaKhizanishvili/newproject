<?php

class o_all_reviewsView extends View
{
	protected $_option = 'o_all_reviews';
	protected $_option_edit = 'o_all_reviews';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'o_all_reviews.display';

	function display( $tmpl = null )
	{
		/* @var $model o_all_reviewssModel */
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
