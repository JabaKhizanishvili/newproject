<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_rsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList( $Full = false )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->position = mb_strtolower( trim( Request::getState( $this->_space, 'position', '' ) ) );
		$Return->tablenum = mb_strtolower( trim( Request::getState( $this->_space, 'tablenum', '' ) ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-3' ) );
		$Return->orgid = (int) trim( Request::getState( $this->_space, 'orgid', '' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '' ) );
		$Return->category = (int) trim( Request::getState( $this->_space, 'category', '-1' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$where = array();

		if ( $Return->orgid )
		{
			$where[] = ' w.org = ' . DB::Quote( $Return->orgid );
			if ( $Return->org_place )
			{
				$where[] = ' w.parent_id in (select ww.parent_id from hrs_workers_sch ww where ww.org_place in( '
								. ' select '
								. ' t.id '
								. ' from lib_units t '
								. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . DB::Quote( $Return->org_place )
								. ' where '
								. ' t.active = 1 '
								. ' and u.id is not null )) ';
			}
			if ( $Return->firstname )
			{
				$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
			}
			if ( $Return->lastname )
			{
				$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
			}
			if ( $Return->tablenum != '' )
			{
				$where[] = ' w.parent_id in (select wss.parent_id from hrs_workers_sch wss  where  lower(wss.tablenum) like ' . DB::Quote( '%' . $Return->tablenum . '%' ) . ')';
			}
			if ( $Return->position )
			{
				$where[] = ' w.parent_id in ('
								. ' select '
								. ' wss.parent_id '
								. ' from hrs_workers_sch wss '
								. ' left join lib_staff_schedules sc on sc.id = wss.staff_schedule '
								. '  where  sc.position in (' . $this->_search( $Return->position, [ 'lib_title' ], 'lib_positions' ) . ')'
								. ' )'
				;
			}
			if ( $Return->category > 0 )
			{
				$where[] = ' w.category_id = ' . DB::Quote( $Return->category );
			}
			if ( $Return->active > -3 )
			{
				$where[] = ' nvl(( SELECT '
								. ' CASE WHEN (max(nvl(sw.active, 0)) = -2) THEN  -1 '
								. ' ELSE max(nvl(sw.active, 0)) END '
								. ' FROM SLF_worker sw WHERE sw.person = w.parent_id and sw.org = ' . (int) $Return->orgid . ') , -1)  = ' . DB::Quote( $Return->active );
			}
//			$where[] = ' sp.active = 1 ';
			$where[] = ' w.id in (select w.orgpid from slf_worker w where w.id is not null group by w.orgpid) ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

			$CountQuery = 'select count (*) from hrs_workers_all w '
							. ' left join slf_persons p on p.id = w.parent_id '
							. ' left join lib_unitorgs lu on lu.id = w.org '
							. ' left join lib_country lc on lc.lib_code = p.country_code '
							. $whereQ
			;
			$Return->total = DB::LoadResult( $CountQuery );
			$Query = 'SELECT '
							. ' w.org_name, '
							. ' w.firstname, '
							. ' w.lastname, '
							. ' w.private_number, '
							. ' nvl(( SELECT '
							. ' CASE WHEN (count(wr.work_type) > 0) '
							. ' THEN 1 ELSE 2 END '
							. ' FROM SLF_worker sw '
							. ' LEFT JOIN lib_staff_schedules ss ON ss.id = sw.staff_schedule '
							. ' LEFT JOIN lib_working_rates wr ON wr.id = ss.working_rate '
							. ' WHERE sw.person = w.parent_id AND wr.work_type = 1 AND sw.active = 1 and sw.org = ' . DB::Quote( $Return->orgid ) . ' ), 1) AS work_type, '
							. ' nvl(( SELECT '
							. ' CASE WHEN (max(nvl(sw.active, 0)) = -2) THEN  -1 '
							. ' ELSE max(nvl(sw.active, 0)) END '
							. ' FROM SLF_worker sw WHERE sw.person = w.parent_id and sw.org = ' . DB::Quote( $Return->orgid ) . ' ) , -1) AS contract_state, '
							. ' to_char(w.birthdate, \'dd-mm-yyyy\') birthdate, '
							. ' w.gender, '
							. ' w.iban, '
							. ' w.email, '
							. ' w.mobile_phone_number, '
							. ' w.nationality, '
							. ' w.org, '
							. ' lc.id country_code, '
							. ' lc.lib_title country_name, '
							. ' w.active '
							. ' FROM hrs_workers_all w '
							. ' left join slf_persons p on p.id = w.parent_id '
							. ' left join lib_unitorgs lu on lu.id = w.org '
							. ' left join lib_country lc on lc.lib_code = p.country_code '
							. $whereQ
							. $order_by
			;
			$Limit_query = 'select k.* '
							. ' from ( '
							. ' select a.*, rownum rn from (' .
							$Query
							. ') a) k where rn > '
							. $Return->start
							. ' and rn <= ' . $Return->limit;
			if ( $Full )
			{
				$Return->items = DB::LoadObjectList( $Query );
			}
			else
			{
				$Return->items = DB::LoadObjectList( $Limit_query );
			}
			$Return->loaded = 1;
		}
		else
		{
			$Return->loaded = 0;
		}
		return $Return;

	}
//
//	public function Export()
//	{
//		$Data = $this->getList( true );
//
//		$Rows = HTML::renderExport( $Data->items, dirname( __FILE__ ) . DS . 'tmpl' . DS . 'export.xml' );
//		$ToNumberIN = array();
//		$name = 'RS-' . date( 'Y-m-d' ) . '-' . time() . '.xls';
//		$filename = X_EXPORT_DIR . DS . $name;
//		$total = $Data->total ? $Data->total + 10 : 5;
//		require_once PATH_BASE . DS . 'libraries' . DS . 'excelxml.php';
//		$Cols = count( $Rows['HEADER'] );
//		if ( empty( $Cols ) )
//		{
//			return false;
//		}
//		$excel = new ExcelXml( $filename, $Cols, $total );
////		$excel->addStyle( 'header', $header_style );
//		$excel->start();
//		$excel->addRow( $Rows['HEADER'] );
//		$ToNumber = array_flip( $ToNumberIN );
//		foreach ( $Rows['ROWS'] as $d )
//		{
//			$add = array();
//			if ( is_array( $d ) || is_object( $d ) )
//			{
//				foreach ( $d as $k => $v )
//				{
//					if ( is_null( $v ) )
//					{
//						$v = Text::_( '' );
//					}
//					else if ( isset( $ToNumber[$k] ) )
//					{
//						$v = Helper::FormatBalance( $v, 2 );
//					}
//					$add[] = $v;
//				}
//				$excel->addRow( $add );
//			}
//		}
//		$excel->storeRow();
//		return $excel->finish( 1 );
//
//	}

}
