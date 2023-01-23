<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class MyTasksModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', 0 ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->tasktitle = trim( Request::getState( $this->_space, 'tasktitle', '' ) );
		$Return->wstate = (int) Request::getState( $this->_space, 'wstate', -1 );
		$order_by = ' order by t.status asc, ' . $order . ' ' . $dir . ', task_create_date desc ';

		$where = array();
		$SubQuery = 'select t.group_id from rel_wgroups t where worker = ' . Users::GetUserID();
		$where[] = 'g.task_actor_group in( ' . $SubQuery . ') ';
		$where[] = 'g.task_actor <> ' . Users::GetUserID();

		$whereUser = array();
		$whereUser[] = ' tg.task_actor = ' . Users::GetUserID();

		if ( $Return->firstname )
		{
			$where[] = ' p.id in (' . $this->_search( $Return->firstname, [ 'FIRSTNAME', 'LASTNAME' ], 'slf_persons' ) . ')';
			$whereUser[] = ' p.id in (' . $this->_search( $Return->firstname, [ 'FIRSTNAME', 'LASTNAME' ], 'slf_persons' ) . ')';
		}
//		if ( $Return->lastname )
//		{
//			$where[] = ' p.lastname like ' . DB::Quote( '%' . $Return->lastname . '%' );
//			$whereUser[] = ' p.lastname like ' . DB::Quote( '%' . $Return->lastname . '%' );
//		}
		if ( $Return->lib_title )
		{
			$where[] = ' f.id in (' . $this->_search( $Return->lib_title, 'LIB_TITLE', 'lib_limit_app_types' ) . ')';
			$whereUser[] = ' f.id in (' . $this->_search( $Return->lib_title, 'LIB_TITLE', 'lib_limit_app_types' ) . ')';
		}
		if ( $Return->org > 0 )
		{
			$where[] = ' w.org =  ' . $Return->org;
			$whereUser[] = ' w.org =  ' . $Return->org;
		}
		if ( $Return->tasktitle )
		{
			$where[] = ' tt.id in (' . $this->_search( $Return->tasktitle, 'LIB_TITLE', 'lib_flow_elements' ) . ')';
			$whereUser[] = ' tt.id in (' . $this->_search( $Return->tasktitle, 'LIB_TITLE', 'lib_flow_elements' ) . ')';
		}
		if ( Xhelp::checkDate( $Return->start_date ) )
		{
			$StartDate = new PDate( $Return->start_date );
			$where[] = ' w.start_date >= to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') ';
			$whereUser[] = ' w.start_date >= to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') ';
		}
		if ( Xhelp::checkDate( $Return->end_date ) )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' w.end_date <= to_date(\'' . $EndDate->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\') ';
			$whereUser[] = ' w.end_date <= to_date(\'' . $EndDate->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\') ';
		}
		if ( $Return->wstate > -1 )
		{
			$where[] = ' w.status =  ' . (int) $Return->wstate;
			$whereUser[] = ' w.status = ' . (int) $Return->wstate;
		}

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$whereQU = count( $whereUser ) ? ' WHERE (' . implode( ') AND (', $whereUser ) . ')' : '';

		$countQuery = ' select count(1) from ( '
						. ' select 1 from hrs_tasks g '
						. ' left join hrs_applications w on w.id = g.workflow_id '
						. ' left join lib_flow_elements tt on tt.id = g.lib_task_id '
						. $whereQ
						. ' union all '
						. ' select 1 from hrs_tasks tg '
						. ' left join hrs_applications w on w.id = tg.workflow_id '
						. ' left join lib_flow_elements tt on tt.id = tg.lib_task_id '
						. $whereQU
						. ' ) t '
		;

		$Return->total = DB::LoadResult( $countQuery );
		$Query = ' select t.*, '
						. ' (case when trunc(sysdate) - trunc(t.task_due_date) > 0 and t.state = 0 then 1 else 0 end ) lateness,'
						. ' decode(t.state, 0, decode(w1.LOG_FLOW, null, 0, 1), 1) LOG'
						. ' from ( '
						. ' select '
						. ' g.*, '
						. ' w.org, '
						. ' w.replacing_workers, '
						. ' p.firstname, '
						. ' p.lastname, '
						. ' null position, '
						. ' p.mobile_phone_number, '
						. ' to_char(w.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(w.end_date, \'dd-mm-yyyy\') end_date, '
						. ' w.day_count, '
						. ' f.LIB_TITLE, '
						. ' to_char(g.task_due_date, \'dd-mm-yyyy\') due_date,  '
						. ' to_char(g.task_create_date, \'dd-mm-yyyy hh24:mi:ss\') wcreate_date, '
						. ' w.status '
						. ' from hrs_tasks g '
						. ' left join lib_flow_elements tt on tt.id = g.lib_task_id '
						. ' left join hrs_applications w on w.id = g.workflow_id '
						. ' left join rel_person_org po on po.id = w.worker '
						. ' left join slf_persons p on p.id = po.person '
						. ' left join lib_limit_app_types f on f.id = w.type '
						. $whereQ
						. ' union all '
						. ' select'
						. ' tg.*,'
						. ' w.org, '
						. ' w.replacing_workers, '
						. ' p.firstname, '
						. ' p.lastname, '
						. ' null position, '
						. ' p.mobile_phone_number,'
						. ' to_char(w.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(w.end_date, \'dd-mm-yyyy\') end_date, '
						. ' w.day_count, '
						. ' f.LIB_TITLE, '
						. ' to_char(tg.task_due_date, \'dd-mm-yyyy\') due_date,  '
						. ' to_char(tg.task_create_date, \'dd-mm-yyyy hh24:mi:ss\') wcreate_date,'
						. ' w.status '
						. ' from hrs_tasks tg '
						. ' left join lib_flow_elements tt on tt.id = tg.lib_task_id '
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
						$Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->items = DB::LoadObjectList( $Limit_query );
		return $Return;

	}

}
