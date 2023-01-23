<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class WorkersInfoModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$Return->text = trim( Request::getState( $this->_space, 'text', false ) );
		$Return->position = trim( Request::getState( $this->_space, 'position', false ) );
		$Return->mobile = Helper::CleanNumber( trim( Request::getState( $this->_space, 'mobile', false ) ) );
		$Return->ext = Helper::CleanNumber( trim( Request::getState( $this->_space, 'ext', false ) ) );
		$Return->office = trim( Request::getState( $this->_space, 'office', false ) );
		$Return->org = (int) Request::getState( $this->_space, 'org', '-1' );
		$Return->query = true;
		$where = array();
		$ww = array();
		$wh = array();
		if ( $Return->text )
		{
			$Phrases = explode( ' ', $Return->text );
			foreach ( $Phrases as $Name )
			{
				$Name = trim( $Name );
				if ( empty( $Name ) )
				{
					continue;
				}
				$where[] = ' t.id in (' . $this->_search( $Return->text, [ 'FIRSTNAME', 'LASTNAME' ], 'slf_persons' ) . ')';
			}
		}
		if ( $Return->position )
		{
			$ww[] = ' ppp.id in (' . $this->_search( $Return->position, [ 'LIB_TITLE' ], 'lib_positions' ) . ')';
			$wh[] = ' ls.position in (select pp.id from lib_positions pp where pp.id in (' . $this->_search( $Return->position, [ 'LIB_TITLE' ], 'lib_positions' ) . '))';
		}
		if ( $Return->mobile )
		{
			$where[] = ' t.mobile_phone_number  like \'%' . $Return->mobile . '%\'';
		}
		$where[] = 't.active =1 ';
		$where[] = 't.id > 0 ';
		if ( count( $ww ) > 0 )
		{
			$ww[] = 'wr.active = 1 ';
			$where[] = 't.id in ( select wr.person from slf_worker wr '
							. ' left join lib_staff_schedules scc on scc.id=wr.staff_schedule '
							. ' left join lib_positions ppp on ppp.id=scc.position '
							. (count( $ww ) ? ' WHERE (' . implode( ') AND (', $ww ) . ')' : '') . ' )';
		}
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from slf_persons t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.*, '
						. ' t.firstname wfirstname, '
						. ' t.lastname wlastname,  '
//						. ' decode( t.work_mobile_phone_number, null,  t.mobile_phone_number, t.work_mobile_phone_number) p_mobile_phone_number, '
						. ' nvl(a.type, -1) apptype, '
						. ' ty.lib_desc app_name, '
						. ' to_char(a.start_date, \'dd-mm-yyyy hh24:mi\') start_date, '
						. ' nvl(ede.real_type_id, 2) real_type_id, '
						. ' to_char(a.end_date, \'dd-mm-yyyy hh24:mi\') end_date '
						. ' from slf_persons t '
						. ' left join hrs_applications a on a.worker = t.id and sysdate between a.start_date and a.end_date and a.status >0  '
						. ' left join lib_applications_types ty on a.type = ty.type '
						. ' left join (select e.staff_id, e.real_type_id, e.event_date, row_number() over(partition by e.staff_id order by e.event_date desc) rn from hrs_staff_events e where e.event_date between sysdate - 5 and sysdate and e.real_type_id in (1, 2, 10, 11)) ede on ede.staff_id = t.id and ede.rn = 1 '
						. $whereQ

		;
		$Limit_query = 'select k.* '
//						. ' getChiefsByWorker(k.id) all_chiefs '
						. ' from ( '
						. ' select a.*, nvl(rt.TIME_ID, 0) work_time, rownum rn from ( ' .
						$Query
						. ') a'
						. ' left join ('
						. ' select '
						. ' * '
						. ' from hrs_v_users_working_time gr '
						. ' where sysdate between gr.start_time and gr.end_time'
						. ' ) rt on rt.worker = a.id '
						. ') k where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit
						. $order_by
		;
		$Return->items = DB::LoadObjectList( $Limit_query, 'ID' );
		$Keys = array_keys( $Return->items );
		$Collect = XHelp::getAssignedWorkers( implode( ', ', $Keys ), $wh );
		foreach ( $Keys as $Key )
		{
			$Return->items[$Key]->ORG = C::_( $Key, $Collect, array() );
		}
		return $Return;

	}

}
