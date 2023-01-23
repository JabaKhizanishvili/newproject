<?php

class getchiefs
{
	public function GetService()
	{
		$data = trim( Request::getVar( 'data' ) );
		Request::setVar( 'format', 'json' );
		$DataList = $this->cleanData( explode( ',', preg_replace( '/[^0-9,]/', '', $data ) ) );
		$Org = trim( Request::getVar( 'org' ) );
		$delete = Request::getVar( 'delete', false );
		if ( $delete )
		{
			$DataListD = array_flip( $DataList );
			if ( isset( $DataListD[$delete] ) )
			{
				unset( $DataListD[$delete] );
			}
			$DataList = array_flip( $DataListD );
		}
		$content = '';
		$Response = new stdClass();
		$Response->idx = '';
		$Response->html = '';
		if ( !empty( $DataList ) )
		{
			$Chiefs = $this->_getWorkers( $DataList, $Org );
			$IDx = array();
			$Response = new stdClass();
			ob_start();
			require 'tmpl.php';
			$content = ob_get_contents();
			$Response->idx = $IDx;
			$Response->html = $content;
			ob_clean();
		}
		return json_encode( $Response );

	}

	protected function _getWorkers( $DataList, $Org )
	{
		$Where = '';
		if ( $Org )
		{
			$Where = ' and u.org = ' . $Org;
		}
		$query = 'select u.id, '
						. ' u.firstname || \' \' || u.lastname  as workername,'
						. ' u.position   '
						. ' from hrs_workers u '
						. ' where u.id in(' . implode( ',', $DataList ) . ')'
						. $Where
						. ' order by  workername '
		;
		return DB::LoadObjectList( $query );

	}

	protected function cleanData( $DataList )
	{
		$Return = array();
		foreach ( $DataList as $d )
		{
			$d = trim( $d );
			if ( !empty( $d ) )
			{
				$Return[] = $d;
			}
		}
		return $Return;

	}

}
