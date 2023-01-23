<?php

class getworker
{
	public function GetService()
	{
		$data = trim( Request::getVar( 'data' ) );
		Request::setVar( 'format', 'json' );
		$Data = (int) trim( preg_replace( '/[^0-9,]/', '', $data ) );
		$content = '';
		$Response = new stdClass();
		$Response->idx = '';
		$Response->html = '';
		if ( !empty( $Data ) )
		{
			$Worker = $this->_getWorker( $Data );
			$Response = new stdClass();
			$IDx = 0;
			ob_start();
			require 'tmpl.php';
			$content = ob_get_contents();
			$Response->idx = $IDx;
			$Response->html = $content;
			ob_clean();
		}
		return json_encode( $Response );

	}

	protected function _getWorker( $Data )
	{
		$query = 'select u.id, '
						. ' u.firstname || \' \' || u.lastname as workername,'
						. 'u.org_name, '
						. 'u.position '
						. ' from hrs_workers u '
						. ' where u.id =' . $Data
		;
		return DB::LoadObject( $query );

	}

}
