<?php

class ajaxworkers
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
			$Where[] = ' u.org in (' . $Org . ')';
		}
		$Where[] = ' u.active=1 ';
		$Where[] = ' sf.active=1 ';
//		$Where[] = ' nvl(u.enable, 0)=1 ';
		$whereQ = count( $Where ) ? ' WHERE (' . implode( ') AND (', $Where ) . ')' : '';
		$query = 'select u.id "data", '
						. ' u.firstname || \' \' || u.lastname || \' - \' || u.org_name || \' - \' || sf.lib_title as "value" '
						. ' from  hrs_workers_sch u '
						. ' left join LIB_STAFF_SCHEDULES sf on sf.id = u.STAFF_SCHEDULE '
						. $whereQ
						. ' order by "data" asc '
		;

		$result = DB::LoadObjectList( $query );
		foreach ( $result as $worker )
		{
			$worker->value = XTranslate::_( $worker->value );
		}

		return $result;

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
