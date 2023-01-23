<?php
define( 'A_B', '|| \' \' ||' );

class slfstaff
{
	public function GetService()
	{
		$Query = trim( Request::getVar( 'query' ) );
		$Case = trim( Request::getVar( 'case' ) );
		$Data = trim( Request::getVar( 'data' ) );

		Request::setVar( 'format', 'json' );
		$Response = new stdClass();

		if ( !empty( $Data ) )
		{
			$this->GET( $Response, $Data, $Case );
		}
		else
		{
			$this->SEARCH( $Response, $Query, $Case );
		}

		return json_encode( $Response );

	}

	protected function SEARCH( &$Response, $Query, $Case = 0 )
	{
		$Response->query = $Query;
		$Response->suggestions = $this->Package( $Query, $Case );
		return;

	}

	protected function GET( &$Response, $data, $Case = 0 )
	{
		$name = trim( Request::getVar( 'name' ) );
		$tmplMode = trim( Request::getVar( 'tmpl' ) );
		$delete = Request::getVar( 'delete', false );
		$DataList = Helper::CleanArray( explode( ',', preg_replace( '/[^0-9,]/', '', $data ) ) );
		$content = '';

		if ( $delete )
		{
			$DataListD = array_flip( $DataList );
			if ( isset( $DataListD[$delete] ) )
			{
				unset( $DataListD[$delete] );
			}
			$DataList = array_flip( $DataListD );
		}

		$Response->idx = '';
		$Response->html = '';
		if ( count( $DataList ) )
		{
			$IDmx = implode( ',', $DataList );
			$Workers = new stdClass();
			$query = '';
			$where = [];
			$tmpl = $tmplMode;
			switch ( $Case )
			{
				default :
				case 1: // Set Selected Persons
					$where[] = ' u.id in (' . $IDmx . ')';
					$where[] = ' u.active = 1';
					$Workers = $this->GetAllPersons( $query, $where, 1 );
					$tmpl = 'multy';
					break;
				case 2: // Set Selected Persons
					$where[] = ' k.id in (' . $IDmx . ')';
					$where[] = ' k.active = 1 ';
					$Workers = $this->GetOrganizationWorkers( $query, $where, 1 );
					$tmpl = 'multy';
					break;
				case 3: // Get Workers from My Organizations
					$where[] = ' u.id in (' . $IDmx . ')';
					$Workers = $this->GetWorkersFromMyOrganizations( $query, $where, 1 );
					$tmpl = 'multy';
					break;
				case 4: // Get My Workers
					$where[] = ' u.id in (' . $IDmx . ')';
					$Workers = $this->GetMyWorkers( $query, $where, 1 );
					$tmpl = 'multy';
					break;
				case 5: // Get All Workers
					$where[] = ' u.id in (' . $IDmx . ')';
					$Workers = $this->GetAllWorkers( $query, $where, 1 );
					$tmpl = $tmpl ? $tmpl : 'single';
					break;
				case 6: // Get Worker of Organization
					break;
				case 7: // Get Workers by Organizations
					$where[] = ' u.id in (' . $IDmx . ')';
					$Workers = $this->GetWorkersByOrganizations( $query, $where, 1 );
					break;
			}
			$Response = new stdClass();
			$IDx = array();
			ob_start();
			require 'tmpl/' . $tmpl . '.php';
			$content = ob_get_contents();
			$Response->idx = $IDx;
			$Response->html = $content;
			ob_clean();
		}
		return;

	}

	protected function Package( $Query, $Case = 0 )
	{
		$QueryData = explode( ' ', $Query );
		$where = array();
		$query = '';
		$model = new Model( [] );
		foreach ( $QueryData as $Key )
		{
			$where[] = ' u.' . (in_array( $Case, [ 1, 2 ] ) ? 'id ' : 'parent_id ') . ' in (' . $model->_search( $Key, [ 'firstname', 'lastname' ], 'slf_persons' ) . ')';
		}

		switch ( $Case )
		{
			default:
			case 1: // Get All Persons
				$where[] = ' u.active=1 ';
				$where[] = ' u.id >0 ';
				$this->GetAllPersons( $query, $where );
				break;
			case 2: // Get Organization Workers
				$org = trim( Request::getVar( 'org' ) );
				$where[] = ' u.active=1 ';
				$where[] = ' u.id > 0 ';
				$where[] = ' k.org = ' . DB::Quote( $org );
				$where[] = ' k.active = 1 ';
				$this->GetOrganizationWorkers( $query, $where );
				break;
			case 3: // Get Workers from My Organizations
				$org = trim( Request::getVar( 'org' ) );
				$where[] = ' u.active=1 ';
				$where[] = ' u.id > 0 ';
				$where[] = ' u.org in (' . $org . ')';
				$this->GetWorkersFromMyOrganizations( $query, $where );
				break;
			case 4: // Get My Workers
				$where[] = ' u.active=1 ';
				$MyIDS = DB::Quote( XGraph::GetMyOrgsIDx );
				$where[] = ' u.id in ()';
				$this->GetMyWorkers( $query, $where );
				break;
			case 5: // Get All Workers
				$where[] = ' u.active=1 ';
				$where[] = ' u.id > 0 ';
				$this->GetAllWorkers( $query, $where );
				break;
			case 6: // Get Worker of Organization
				break;
			case 7: // Get Workers by Organizations
				$where[] = ' u.active=1 ';
				$where[] = ' u.id > 0 ';
				$this->GetWorkersByOrganizations( $query, $where );
				break;
		}

		if ( empty( $query ) )
		{
			return [];
		}

		$result = DB::LoadObjectList( $query );
		foreach ( $result as $worker )
		{
			$rr = [];
			$ex = explode( '-', $worker->value );
			foreach ( $ex as $val )
			{
				$value = trim( $val );
				if ( empty( $value ) )
				{
					continue;
				}

				$rr[] = XTranslate::_( $value );
			}

			$worker->value = implode( ' - ', $rr );
		}
		return $result;

	}

	protected function GetWorkersByOrganizations( &$query, $where = [], $get = 0 )
	{
		$Where = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$case_lbl = $this->lbl_format( [ 'u.firstname' . A_B . 'u.lastname', 'u.org_name' ], ' - ' );

		$query = 'select '
						. 'u.id "data", ' . $case_lbl . ' as "value" '
						. ' from  hrs_workers u '
						. $Where
						. ' order by "data" asc '
		;
		if ( $get == 1 )
		{
			$query = 'select '
							. ' u.id, ' . $case_lbl . ' as workername '
							. ' from  hrs_workers u '
							. $Where
			;
			return DB::LoadObjectList( $query );
		}
		return;

	}

	protected function GetAllWorkers( &$query, $where = [], $get = 0 )
	{
		$Where = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$case_lbl = $this->lbl_format( [ 'u.firstname' . A_B . 'u.lastname', 's.lib_title', 'u.org_name' ], ' - ', 0, 'u' );

		$query = 'select '
						. 'u.id "data", ' . $case_lbl . ' as "value" '
						. ' from  hrs_workers_sch u '
						. ' left join lib_staff_schedules s on s.id = u.staff_schedule '
						. $Where
						. ' order by "data" asc '
		;
		if ( $get == 1 )
		{
			$query = 'select '
							. ' u.id, ' . $case_lbl . ' as workername '
							. ' from  hrs_workers_sch u '
							. ' left join lib_staff_schedules s on s.id = u.staff_schedule '
							. $Where
			;
			return DB::LoadObjectList( $query );
		}
		return;

	}

	protected function GetMyWorkers( &$query, $where = [], $get = 0 )
	{
		$Where = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$case_lbl = $this->lbl_format( [ 'u.firstname' . A_B . 'u.lastname', 's.lib_title', 'u.org_name' ], ' - ', 0, 'u' );

		$query = 'select '
						. 'u.id "data", ' . $case_lbl . ' as "value" '
						. ' from  hrs_workers_sch u '
						. ' left join lib_staff_schedules s on s.id = u.staff_schedule '
						. $Where
						. ' order by "data" asc '
		;
		if ( $get == 1 )
		{
			$query = 'select '
							. ' u.id, ' . $case_lbl . ' as workername '
							. ' from  hrs_workers_sch u '
							. ' left join lib_staff_schedules s on s.id = u.staff_schedule '
							. $Where
			;
			return DB::LoadObjectList( $query );
		}
		return;

	}

	protected function GetWorkersFromMyOrganizations( &$query, $where = [], $get = 0 )
	{
		$Where = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$case_lbl = $this->lbl_format( [ 'u.firstname' . A_B . 'u.lastname', 'u.position', 'u.org_name' ], ' - ', 0, 'u' );

		$query = 'select '
						. 'u.id "data", ' . $case_lbl . ' as "value" '
						. ' from  hrs_workers_sch u '
						. $Where
						. ' order by "data" asc '
		;
		if ( $get == 1 )
		{
			$query = 'select '
							. ' u.id, ' . $case_lbl . ' as workername '
							. ' from hrs_workers_sch u '
							. $Where
			;
			return DB::LoadObjectList( $query );
		}
		return;

	}

	protected function GetAllPersons( &$query, $where = [], $get = 0 )
	{
		$Where = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$case_lbl = $this->lbl_format( [ 'u.firstname' . A_B . 'u.lastname', 'u.email' ], ' - ' );

		$query = 'select '
						. 'u.id "data", ' . $case_lbl . ' as "value" '
						. ' from  slf_persons u '
						. $Where
						. ' order by "data" asc '
		;
		if ( $get == 1 )
		{
			$query = 'select '
							. ' u.id, ' . $case_lbl . ' as workername '
							. ' from slf_persons u '
							. $Where
			;
			return DB::LoadObjectList( $query );
		}
		return;

	}

	protected function GetOrganizationWorkers( &$query, $where = [], $get = 0 )
	{
		$Where = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$case_lbl = $this->lbl_format( [ 'u.firstname' . A_B . 'u.lastname', 'lu.lib_title', 'lss.lib_title' ], ' - ', 0, 'k' );

		$query = 'select k.id "data", '
						. $case_lbl
						. ' as "value" '
						. ' from  slf_worker k '
						. ' left join slf_persons u on u.id = k.person '
						. ' left join lib_unitorgs lu on lu.id = k.org '
						. ' left join lib_staff_schedules lss on lss.id = k.staff_schedule '
						. $Where
						. ' order by "data" asc '
		;
		if ( $get == 1 )
		{
			$query = 'select k.id, '
							. $case_lbl
							. ' as workername '
							. ' from  slf_worker k '
							. ' left join slf_persons u on u.id = k.person '
							. ' left join lib_unitorgs lu on lu.id = k.org '
							. ' left join lib_staff_schedules lss on lss.id = k.staff_schedule '
							. $Where
			;
			return DB::LoadObjectList( $query );
		}
		return;

	}

	protected function lbl_format( $keys = [], $separator = '', $skip = 0, $alt_alias = '' )
	{
		$config_based = Helper::getConfig( 'apps_worker_identificator' );
		$ex = array_diff( explode( '|', $config_based ), [ '-1' ] );

		$collect = [];
		foreach ( $ex as $i => $k )
		{
			if ( empty( $k ) )
			{
				continue;
			}

			$add = 'u.' . $k;
			if ( $k == 'tablenum' )
			{
				if ( !empty( $alt_alias ) )
				{
					$add = $alt_alias . '.' . $k;
				}
				else
				{
					continue;
				}
			}

			if ( !in_array( $add, $keys ) )
			{
				if ( $k == 'birthdate' )
				{
					$add = ' to_char(' . $add . ', \'yyyy-mm-dd\') ';
				}

				$keys[] = $add;
			}
		}

		foreach ( $keys as $key )
		{
			$collect[] = ' ' . $key . ' ';
		}
		return implode( ' || \'' . $separator . '\' || ', $collect );

	}

}
