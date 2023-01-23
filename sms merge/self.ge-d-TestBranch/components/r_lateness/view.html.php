<?php

class r_latenessView extends View
{
	protected $_option = 'r_lateness';
	protected $_option_edit = 'r_lateness';
	protected $_order = 'ID';
	protected $_dir = '1';
	protected $_space = 'r_lateness.display';

	function display( $tmpl = null )
	{
		/* @var $model r_latenesssModel */
		$model = $this->getModel();
		$data = $model->getList();
		$this->assignRef( 'data', $data );
//        $task = Request::getVar('task');
//
//        switch ($task) {
//            case 'export':
//                echo '<pre>';
//                print_r($model->Export());
//                echo '</pre>';
//                exit;;
//                break;
//        }
		parent::display( $tmpl );
	}

}
