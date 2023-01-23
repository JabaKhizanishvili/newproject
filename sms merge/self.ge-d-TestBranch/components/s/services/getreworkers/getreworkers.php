<?php

class getreworkers
{
	public function GetService()
	{
		$data = trim( Request::getVar( 'data' ) );
		$ID = trim( Request::getVar( 'id' ) );
		Request::setVar( 'format', 'json' );
		$DataList = $this->cleanData( explode( ',', preg_replace( '/[^0-9,]/', '', $data ) ) );
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
			$Workers = $this->_getWorkers( $DataList, $ID );
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

	protected function _getWorkers( $DataList, $ID )
	{
		$query = 'select u.id, '
						. ' nvl(rg.ordering, 999) ordering, '
						. ' u.firstname || \' \' || u.lastname as workername '
						. ' from ' . DB_SCHEMA . '.slf_persons u '
						. ' left join ' . DB_SCHEMA . '.rel_worker_worker rg on rg.reworker = u.id '
						. ' and rg.worker = ' . $ID
						. ' where u.id in(' . implode( ',', $DataList ) . ')'
						. ' and u.active=1 '
						. ' order by  ordering asc, workername '
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
