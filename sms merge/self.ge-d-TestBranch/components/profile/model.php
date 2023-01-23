<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class ProfileModel extends Model
{
	public function getWorker()
	{
		return Users::getUser();
//		return XRedis::getDBCache( slf_persons, $query, 'LoadObject' );
//		return DB::LoadObject( $query );

	}

	public function getWorkerOrgData()
	{
		$query = 'select '
						. ' t.* '
						. ' from hrs_workers_org t '
						. ' where t.parent_id = ' . Users::GetUserID()
						. ' and t.enable = 1 '
		;
		return XRedis::getDBCache( 'hrs_workers_org', $query, 'LoadObjectList' );
//		return DB::LoadObjectList( $query );

	}

	public function GetBdays( $range = 1, $default = 1 )
	{
		$N = $default;
		if ( $range > 0 )
		{
			$N = $range;
		}
		$N--;
		$Query = 'select '
						. ' t.id, '
						. ' t.firstname, '
						. ' t.lastname, '
						. ' t.birthdate, '
						. ' t.photo, '
						. ' case when '
						. ' to_char(t.birthdate, \'mm-dd\') < to_char(sysdate, \'mm-dd\') then \'9\' || to_char(t.birthdate, \'mm-dd\') '
						. ' else to_char(t.birthdate, \'mm-dd\') end '
						. ' o, '
						. ' to_char(t.birthdate, \'mm-dd\') k '
						. ' from slf_persons t '
						. ' where '
						. ' t.id > 0 '
						. ' and t.active = 1 '
						. ' and t.bday_show = 1 '
						. ' and to_char(t.birthdate, \'mm-dd\') in ('
						. ' select to_char(trunc(sysdate) + level - 1, \'mm-dd\') '
						. ' from dual '
						. ' connect by level <= trunc(sysdate + ' . $N . ') - trunc(sysdate) + 1 '
						. ' ) '
						. ' order by o asc '
		;
		$Scope = 'slf_persons-GetBdays-' . PDate::Get()->toFormat( '%Y-%m-%d' );
		$Data = (array) XRedis::getDBCache( $Scope, $Query, 'LoadObjectList' );
//		$Data = DB::LoadObjectList( $query );
		$Return = array();
		foreach ( $Data as $Item )
		{
			$Key = C::_( 'K', $Item );
			$Return[$Key] = C::_( $Key, $Return, array() );
			$Return[$Key][] = $Item;
		}
		return $Return;

	}

	public function GetNews()
	{
		$query = 'select '
						. 't.*,'
						. ' to_char(t.publish_date, \'dd-mm-yyyy\') v_publish_date '
						. ' from news t '
						. ' where '
						. ' t.active = 1 '
						. ' and sysdate between t.publish_date and t.unpublish_date '
						. ' order by t.publish_date desc'

		;
		$Data = XRedis::getDBCache( 'news', $query );
//		$Data = DB::LoadObjectList( $query );
		return (array) $Data;

	}

	public function GetApps()
	{
		$myids = XGraph::getWorkerIDx();
		if ( !count( $myids ) )
		{
			return [];
		}
		$Query = 'SELECT '
						. ' orgg.LIB_TITLE org_name, '
						. ' llat.*, '
						. ' t.count, '
						. ' t.org orgid, '
						. ' t.c_limit - t.count rest '
						. ' FROM LIB_LIMIT_APP_TYPES llat '
						. ' LEFT JOIN lib_user_holiday_limit t '
						. ' ON t.HTYPE = llat.id '
						. ' LEFT JOIN lib_unitorgs orgg ON orgg.ID = t.org '
						. ' WHERE '
						. ' llat.SHOW_ON_PROFILE = 1'
						. ' AND llat.ACTIVE = 1 '
						. ' AND t.worker in (' . implode( ',', $myids ) . ')'
						. ' AND ( trunc(sysdate) BETWEEN t.start_date AND t.end_date ) '
						. ' ORDER BY llat.ORDERING ASC '
		;
		$Data = (array) XRedis::getDBCache( 'lib_user_holiday_limit', $Query, 'LoadObjectList' );
//		$Data = DB::LoadObjectList( $Query );
		return $Data;

	}

	public function getSessions( $Full = false )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->log_ip = trim( Request::getState( $this->_space, 'log_ip', '' ) );
		$Return->browser_name = trim( Request::getState( $this->_space, 'browser_name', '' ) );
		$Return->os_name = trim( Request::getState( $this->_space, 'os_name', '' ) );

		$dir = ($Return->dir == 0) ? 'desc' : 'asc';
		$order = $Return->order ? $Return->order : 'w.log_date';
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->log_ip )
		{
			$where[] = ' w.log_ip like ' . DB::Quote( '%' . $Return->log_ip . '%' );
		}
		if ( $Return->browser_name )
		{
			$where[] = ' w.browser_name  like ' . DB::Quote( '%' . $Return->browser_name . '%' );
		}
		if ( $Return->os_name )
		{
			$where[] = ' w.os_name like ' . DB::Quote( '%' . $Return->os_name . '%' );
		}

		$where[] = ' w.id >0 ';
		$where[] = ' w.log_user_id >0 ';
		$where[] = ' w.log_user_id = ' . DB::Quote( Users::GetUserID() );
//		$where[] = ' w.username = ' . DB::Quote( C::_( 'LDAP_USERNAME', Users::getUser() ) ) ;

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from SYSTEM_SESSIONS w '
						. ' left join hrs_workers wr on wr.id = w.log_user_id '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' wr.firstname || \' \' || wr.lastname worker, '
//						. ' wr.position, '
						. ' w.id, '
						. ' w.log_ip, '
						. ' w.browser_name, '
						. ' w.os_name, '
						. ' w.log_user_id, '
						. ' w.start_date, '
						. ' w.end_date, '
						. ' w.log_date '
						. ' from SYSTEM_SESSIONS w '
						. ' left join hrs_workers wr on wr.id = w.log_user_id '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
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
		return $Return;

	}

	public function DeleteAllSession()
	{
		$Q = 'DELETE FROM SYSTEM_SESSIONS ss WHERE ss.SESSION_ID != ' . DB::Quote( Session::getSessionID() )
						. ' AND(ss.log_user_id = ' . DB::Quote( Users::GetUserID() ) . ') '
		;
		return DB::Delete( $Q );

	}

	public function DeleteSession( $data )
	{
		$all = [];
		foreach ( $data as $key => $id )
		{
			$all[] = DB::Quote( $id );
		}
		$Q = 'DELETE FROM SYSTEM_SESSIONS WHERE ID IN (' . implode( ',', $all ) . ')';
		return DB::Delete( $Q );

	}

	public function getMyTasks()
	{
		$order_by = ' order by t.task_create_date , task_create_date desc ';
		$where = array();
		$whereUser = array();
		$where[] = ' w.status = 0 ';
		$whereUser[] = ' tg.state = 0 ';
		$whereUser[] = ' w.status = 0 ';
		$SubQuery = 'select t.group_id from rel_wgroups t where worker = ' . Users::GetUserID();
		$where[] = 'g.task_actor_group in( ' . $SubQuery . ') ';
		$where[] = 'g.task_actor <> ' . Users::GetUserID();
		$whereUser[] = ' tg.task_actor = ' . Users::GetUserID();

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$whereQU = count( $whereUser ) ? ' WHERE (' . implode( ') AND (', $whereUser ) . ')' : '';
		$query = ' select t.*, '
						. ' (case when trunc(sysdate) - trunc(t.task_due_date) > 0 and t.state = 0 then 1 else 0 end ) lateness,'
						. ' decode(t.state, 0, decode(w1.LOG_FLOW, null, 0, 1), 1) LOG'
						. ' from ( '
						. ' select '
						. ' g.*, '
						. ' p.firstname, '
						. ' p.lastname, '
						. ' null position, '
						. ' p.mobile_phone_number, '
						. ' to_char(w.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(w.end_date, \'dd-mm-yyyy\') end_date, '
						. ' w.day_count, '
						. ' f.LIB_TITLE, '
						. ' to_char(g.task_due_date, \'dd-mm-yyyy\') due_date,  '
						. ' to_char(g.task_create_date, \'dd-mm-yyyy hh24:mi:ss\') wcreate_date  '
						. ' from hrs_tasks g '
						. ' left join hrs_applications w on w.id = g.workflow_id '
						. ' left join rel_person_org po on po.id = w.worker '
						. ' left join slf_persons p on p.id = po.person '
						. ' left join lib_limit_app_types f on f.id = w.type '
						. $whereQ
						. ' union all '
						. ' select'
						. ' tg.*,'
						. ' p.firstname, '
						. ' p.lastname, '
						. ' null position, '
						. ' p.mobile_phone_number, '
						. ' to_char(w.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(w.end_date, \'dd-mm-yyyy\') end_date, '
						. ' w.day_count, '
						. ' f.LIB_TITLE, '
						. ' to_char(tg.task_due_date, \'dd-mm-yyyy\') due_date,  '
						. ' to_char(tg.task_create_date, \'dd-mm-yyyy hh24:mi:ss\') wcreate_date  '
						. ' from hrs_tasks tg '
						. ' left join hrs_applications w on w.id = tg.workflow_id '
						. ' left join rel_person_org po on po.id = w.worker '
						. ' left join slf_persons p on p.id = po.person '
						. ' left join lib_limit_app_types f on f.id = w.type '
						. $whereQU
						. ' ) t'
						. '   left join hrs_workflow_log w1 '
						. 'on w1.LOG_TYPE = ' . DB::Quote( 'view' )
						. ' and w1.LOG_USER = ' . DB::Quote( Users::GetUserID() )
						. ' and w1.LOG_FLOW = t.WORKFLOW_ID    and w1.LOG_TASK = t.TASK_ID '
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$query
						. ') a) where rn > 0'
						. ' and rn <= 3';
		return (array) XRedis::getDBCache( 'hrs_tasks', $Limit_query, 'LoadObjectList' );
//		return DB::LoadObjectList( $Limit_query );

	}

	public function collect_ptime( $org = 0, $limit = 0 )
	{
		static $collect_ptime = null;
		if ( is_null( $collect_ptime ) )
		{
			$person = Users::GetUserID();
			$query = ' select '
							. ' r.id, '
							. ' r.org '
							. ' from rel_person_org r '
							. ' left join lib_unitorgs lu on lu.id = r.org '
							. ' where '
							. ' r.person = ' . (int) $person
							. ' and r.active = 1 '
							. ' and lu.active = 1 '
			;
			$result = DB::LoadObjectList( $query );

			$collect = [];
			foreach ( $result as $data )
			{
				$RestMinutes = Helper::getRemPrivateTime( $data->ID, 1 );
				$UsedTime = $limit - $RestMinutes;
				$collect[$data->ORG]['RESTMINUTES'] = $RestMinutes;
				$collect[$data->ORG]['USEDTIME'] = $UsedTime;
			}

			$collect_ptime = $collect;
		}

		return C::_( $org, $collect_ptime, [] );

	}

}
