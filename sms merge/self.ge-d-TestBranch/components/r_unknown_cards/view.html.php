<?php

class r_unknown_cardsView extends View
{
	protected $_option = ' r_unknown_cards';
	protected $_option_edit = ' r_unknown_cards';
	protected $_order = 't.rec_date';
	protected $_dir = '1';
	protected $_space = ' r_unknown_cards.display';

	function display( $tmpl = null )
	{
		/* @var $model  r_unknown_cardssModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
