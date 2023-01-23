<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class usertablesModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->BILL_ID = (int) trim( Request::getState( $this->_space, 'BILL_ID', '-1' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->staffschedule = (int) trim( Request::getState( $this->_space, 'staffschedule', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$Return->loaded = 0;
		if ( $Return->BILL_ID > 0 )
		{
			$where = array();
			$where[] = '  t.BILL_ID =  ' . DB::Quote( $Return->BILL_ID );

			if ( $Return->org )
			{
				$where[] = ' w.org =' . $Return->org;
			}
			if ( $Return->staffschedule > 0 )
			{
				$where[] = ' w.staff_schedule=  ' . DB::Quote( $Return->staffschedule );
			}

			$where[] = ' t.status >= ' . Helper::getConfig('show_employee_personal_work_time', 0);
			$where[] = ' w.parent_id =  ' . Users::GetUserID();

			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$countQuery = 'select count(*) from  hrs_table t '
							. ' left join hrs_workers_sch w on w.id = t.worker '
							. ' left join slf_persons a on a.id = t.approve '
							. $whereQ
			;
			$Return->total = DB::LoadResult( $countQuery );
			$Query = 'select '
							. ' w.id, '
							. ' w.org, '
							. ' w.staff_schedule, '
							. ' w.private_number, '
							. ' w.firstname wfirstname, '
							. ' w.lastname wlastname,  '
							. ' a.firstname afirstname, '
							. ' a.lastname alastname,  '
							. ' nvl(t.STATUS, -1) status, '
							. ' t.bill_id,'
							. ' t.sumhour, '
							. ' t.approve, '
							. ' t.holiday, '
							. ' t.bulletins, '
							. ' t.overtimehour, '
							. ' to_char(t.approve_date, \'dd-mm-yyyy\') approve_date '
							. ' from hrs_table t '
							. ' left join hrs_workers_sch w on w.id = t.worker '
							. ' left join slf_persons a on a.id = t.approve '
							. $whereQ
							. $order_by
			;
			$Limit_query = 'select * from ( '
							. ' select a.*, rownum rn from (' .
							$Query
							. ') a) where rn > '
							. $Return->start
							. ' and rn <= ' . $Return->limit;
			$Return->items = DB::LoadObjectList( $Limit_query );
			$Return->loaded = 1;
		}
		else
		{
			$Return->BILL_ID = '';
		}
		return $Return;

	}

}
