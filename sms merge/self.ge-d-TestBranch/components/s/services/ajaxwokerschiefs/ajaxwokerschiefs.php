<?php

class ajaxwokerschiefs
{
	public function GetService()
	{
		$Query = trim( Request::getVar( 'query' ) );
		Request::setVar( 'format', 'json' );
		$Response = new stdClass();
		$Response->query = $Query;
		$Workers = $this->_getWorkers( $Query );
		foreach ( $Workers as $Key => $Worker )
		{
			$Chiefs = $this->_getChiefs( C::_( 'WORKER', $Worker ) );
			if ( is_array( $Workers ) )
			{
				$Workers[$Key]->data = implode( ',', $Chiefs );
			}
		}
		$Response->suggestions = $Workers;
		return json_encode( $Response );

	}

	protected function _getWorkers( $Query )
	{
		$QueryData = explode( ' ', $Query );
		$Where = array();
		foreach ( $QueryData as $Key )
		{
			$Where[] = ' (u.firstname like ' . DB::Quote( '%' . $Key . '%' )
							. ' or u.lastname  like ' . DB::Quote( '%' . $Key . '%' ) . ')';
		}
		$Where[] = ' u.active=1 ';
		$whereQ = count( $Where ) ? ' WHERE (' . implode( ') AND (', $Where ) . ')' : '';
		$query = 'select u.id worker, '
						. ' u.firstname || \' \' || u.lastname  || \' - \' || u.position  as "value" '
						. ' from hrs_workers u '
						. $whereQ
						. ' order by "value" asc '
		;
		return DB::LoadObjectList( $query );

	}

	public function _getChiefs( $Query )
	{
		$query = 'select '
						. ' t.chief '
						. ' from REL_WORKER_CHIEF t '
						. ' where '
						. ' t.worker = ' . DB::Quote( $Query )
		;
		return DB::LoadList( $query );

	}

}
