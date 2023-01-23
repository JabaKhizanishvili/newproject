<?php

class ajaxchiefs
{
	public function GetService()
	{
		$Query = trim( Request::getVar( 'query' ) );
		$Org = trim( Request::getVar( 'org' ) );
		Request::setVar( 'format', 'json' );
		$Response = new stdClass();
		$Response->query = $Query;
		$Response->suggestions = $this->_getWorkers( $Query, $Org );
		return json_encode( $Response );

	}

	protected function _getWorkers( $Query, $Org )
	{
		$QueryData = explode( ' ', $Query );
		$Where = array();
		foreach ( $QueryData as $Key )
		{
			$Where[] = ' (u.firstname like ' . DB::Quote( '%' . $Key . '%' )
							. ' or u.lastname  like ' . DB::Quote( '%' . $Key . '%' ) . ')';
		}
		if ( $Org )
		{
			$Where[] = ' u.org = ' . $Org;
		}
		$Where[] = ' u.active=1 ';
		$Where[] = ' u.user_type = 2 ';
		$whereQ = count( $Where ) ? ' WHERE (' . implode( ') AND (', $Where ) . ')' : '';
		$query = 'select u.id "data", '
						. ' u.firstname || \' \' || u.lastname  || \' - \' || u.position  as "value" '
						. ' from hrs_workers u '
//						. ' left join hrs_workers_org o on o.parent_id = u.id '
						. $whereQ
						. ' order by "value" asc '
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
