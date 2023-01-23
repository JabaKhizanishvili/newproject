<?php

class SView extends View
{
	protected $_option = 's';
	protected $_option_edit = 's';

	function display( $tmpl = null )
	{
		$task = mb_strtolower( trim( Request::getVar( 'service', '' ) ) );
		$data = '';
		$this->CheckTaskPermision( $task );

		$ServiceFile = dirname( __FILE__ ) . DS . 'services' . DS . $task . DS . $task . '.php';
		if ( is_file( $ServiceFile ) )
		{
			require_once $ServiceFile;
			$Service = new $task();
			$data = $Service->GetService();
		}
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
