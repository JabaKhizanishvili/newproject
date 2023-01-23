<?php

class ajaxchiefsworkers
{
	public function GetService()
	{
		$Query = trim( Request::getVar( 'query' ) );
		Request::setVar( 'format', 'json' );
		$Response = new stdClass();
		$Response->query = $Query;
		$Chiefs = $this->_getChiefs( $Query );
		foreach ( $Chiefs as $Key => $Chief )
		{
			$Workers = $this->_getWorkers( C::_( 'CHIEF', $Chief ) );
			if ( is_array( $Workers ) )
			{
				$Chiefs [$Key]->data = implode( ',', $Workers );
			}
		}
		$Response->suggestions = $Chiefs;
		return json_encode( $Response );

	}

	protected function _getWorkers( $Chief )
	{
		$query = 'select '
						. ' t.worker '
						. ' from REL_WORKER_CHIEF t '
						. ' where '
						. ' chief = ' . DB::Quote( $Chief )
		;
		return DB::LoadList( $query );

	}

	public function _getChiefs( $Query )
	{
		$QueryData = explode( ' ', $Query );
		$Where = array();
		foreach ( $QueryData as $Key )
		{
			$Where[] = ' (u.firstname like ' . DB::Quote( '%' . $Key . '%' )
							. ' or u.lastname  like ' . DB::Quote( '%' . $Key . '%' ) . ')';
		}
		$Where[] = ' u.active=1 ';
		$Where[] = ' u.user_type = 2 ';
		$whereQ = count( $Where ) ? ' WHERE (' . implode( ') AND (', $Where ) . ')' : '';
		$query = 'select u.id chief, '
						. ' u.firstname || \' \' || u.lastname  || \' - \' || u.position  as "value" '
						. ' from ' . DB_SCHEMA . '.hrs_workers_data u '
						. $whereQ
						. ' order by "value" asc '
		;
		return DB::LoadObjectList( $query );

	}

}
