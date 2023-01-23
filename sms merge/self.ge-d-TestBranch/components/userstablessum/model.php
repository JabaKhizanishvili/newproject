<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class userstablessumModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->bill_id = (int) trim( Request::getState( $this->_space, 'bill_id', '-1' ) );
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->position = mb_strtolower( trim( Request::getState( $this->_space, 'position', '' ) ) );
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$Return->loaded = 0;
		if ( $Return->bill_id > 0 )
		{
			$where = array();
//			$where[] = ' w.active = 1 ';
//			$where[] = ' w.org = 0 ';
			$where[] = ' w.calculus_type in(1, 2) ';
			$where[] = ' w.id in (select wc.worker from rel_worker_chief wc where wc.chief in (select m.id from hrs_workers m where m.PARENT_ID =  ' . Users::GetUserID() . ' )) ';
			$where[] = ' t.bill_id =  ' . $Return->bill_id;
			if ( $Return->firstname )
			{
				$where[] = ' w.firstname like ' . DB::Quote( '%' . $Return->firstname . '%' );
			}
			if ( $Return->lastname )
			{
				$where[] = ' w.lastname like ' . DB::Quote( '%' . $Return->lastname . '%' );
			}
			if ( $Return->position )
			{
				$where[] = ' lower(w.position) like ' . DB::Quote( '%' . $Return->position . '%' );
			}
			if ( $Return->unit )
			{
				$where[] = ' w.org_place in( '
								. ' select '
								. ' t.id '
								. ' from lib_units t '
								. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . $Return->unit
								. ' where '
								. ' t.active = 1 '
								. ' and u.id is not null )'
				;
			}
			$Return->UNITS = Units::getUnitList();

			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$countQuery = 'select '
							. ' count(*) '
							. ' from hrs_table t '
							. ' left join hrs_workers w on w.id = t.worker '
							. $whereQ
			;
			$Return->total = DB::LoadResult( $countQuery );
			$Query = 'select '
//							. ' w.id, '
							. ' w.private_number, '
							. ' w.tablenum,'
							. ' w.lastname || \' \' || w.firstname workername, '
							. ' c.lastname || \' \' || c.firstname chiefname, '
							. ' w.position, '
							. ' nvl(t.STATUS, -1) status, '
							. ' t.BILL_ID, '
							. ' t.DAY01, '
							. ' t.DAY02, '
							. ' t.DAY03, '
							. ' t.DAY04, '
							. ' t.DAY05, '
							. ' t.DAY06, '
							. ' t.DAY07, '
							. ' t.DAY08, '
							. ' t.DAY09, '
							. ' t.DAY10, '
							. ' t.DAY11, '
							. ' t.DAY12, '
							. ' t.DAY13, '
							. ' t.DAY14, '
							. ' t.DAY15, '
							. ' t.DAYSUM01, '
							. ' t.DAY16, '
							. ' t.DAY17, '
							. ' t.DAY18, '
							. ' t.DAY19, '
							. ' t.DAY20, '
							. ' t.DAY21, '
							. ' t.DAY22, '
							. ' t.DAY23, '
							. ' t.DAY24, '
							. ' t.DAY25, '
							. ' t.DAY26, '
							. ' t.DAY27, '
							. ' t.DAY28, '
							. ' t.DAY29, '
							. ' t.DAY30, '
							. ' t.DAY31, '
							. ' t.DAYSUM02, '
							. ' t.DAYSUM, '
							. ' t.SUMHOUR, '
							. ' t.OVERTIMEHOUR, '
							. ' t.NIGHTHOUR, '
							. ' t.HOLIDAYHOUR, '
							. ' t.OTHERHOUR, '
							. ' t.BULLETINS, '
							. ' t.HOLIDAY, '
							. ' t.NHOLIDAY, '
							. ' t.OTHER, '
							. ' t.HOLIDAYS, '
							. ' t.APPROVE, '
							. ' to_char(t.APPROVE_DATE, \'hh24:mi:ss dd-mm-yyyy\') APPROVE_DATE '
							. ' from hrs_table t '
							. ' left join hrs_workers w on w.id = t.worker and t.bill_id =  ' . DB::Quote( $Return->bill_id )
							. ' left join hrs_workers c on c.id = t.approve and t.bill_id =  ' . DB::Quote( $Return->bill_id )
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
			if ( count( $Return->items ) )
			{
				$Return->loaded = 1;
			}
			else
			{
				$Return->loaded = 2;
			}
		}
		else
		{
			$Return->bill_id = '';
		}
		return $Return;

	}

}
