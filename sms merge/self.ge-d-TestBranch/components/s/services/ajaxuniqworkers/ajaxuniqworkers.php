<?php

class ajaxuniqworkers
{
	public function GetService()
	{
		$Query = trim( Request::getVar( 'query' ) );
		$Org = trim( Request::getVar( 'org' ) );
		$type = trim( Request::getVar( 'type' ) );
		Request::setVar( 'format', 'json' );
		$Response = new stdClass();
		$Response->query = $Query;
		$Response->suggestions = $this->_getWorkers( $Query, $Org, $type );
		return json_encode( $Response );

	}

	protected function _getWorkers( $Query, $Org, $type = '' )
	{
		$QueryData = explode( ' ', $Query );
		$Where = array();

		if ( $type == 'assigned' )
		{
			$Where[] = ' k.active=1 ';
			$Where[] = ' k.id >0 ';
		}

		foreach ( $QueryData as $Key )
		{
//			$Key = Xhelp::lng_chars( $Key, 'en', 'ge' );
			$Where[] = ' (u.firstname like ' . DB::Quote( '%' . $Key . '%' )
							. ' or u.lastname  like ' . DB::Quote( '%' . $Key . '%' ) . ')';
		}
		if ( $Org > 0 )
		{
			$Where[] = ' k.org = ' . DB::Quote( $Org );
		}
		$Where[] = ' u.active=1 ';
		$Where[] = ' u.id >0 ';
//		$Where[] = ' nvl(u.enable, 0)=1 ';
		$whereQ = count( $Where ) ? ' WHERE (' . implode( ') AND (', $Where ) . ')' : '';

		$query = 'select u.id "data", '
						. ' u.firstname || \' \' || u.lastname || \' - \' || u.email || \' - \' ||  u.private_number as "value" '
						. ' from  slf_persons u '
						. $whereQ
						. ' order by "data" asc '
		;
		if ( $type == 'assigned' )
		{
			$query = 'select k.id "data", '
							. ' u.firstname || \' \' || u.lastname || \' - \' || lu.lib_title || \' - \' ||  lss.lib_title as "value" '
							. ' from  slf_worker k '
							. ' left join slf_persons u on u.id = k.person '
							. ' left join lib_unitorgs lu on lu.id = k.org '
							. ' left join lib_staff_schedules lss on lss.id = k.staff_schedule '
							. $whereQ
							. ' order by "data" asc '
			;
		}

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
