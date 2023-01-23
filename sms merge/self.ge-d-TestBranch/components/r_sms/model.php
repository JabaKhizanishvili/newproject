<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_smsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->from_date = trim( Request::getState( $this->_space, 'from_date', '' ) );
		$Return->to_date = trim( Request::getState( $this->_space, 'to_date', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->org > 0 )
		{
			$where[] = ' w.org =' . DB::Quote( $Return->org );
		}

        if (!Xhelp::checkDate($Return->from_date)) {
            $Return->from_date = '';
        }

        if (!Xhelp::checkDate($Return->to_date)) {
            $Return->to_date = '';
        }

        if (!empty($Return->from_date)) {
            $fromDate = new PDate($Return->from_date);
            $where[] = ' t.log_date >= to_date(\'' . $fromDate->toFormat('%d-%m-%Y') . '\', \'dd-mm-yyyy\')';
        }

        if (!empty($Return->to_date)) {
            $toDate = new PDate($Return->to_date);
            $where[] = ' t.log_date <= to_date(\'' . $toDate->toFormat('%d-%m-%Y 23:59:59') . '\', \'dd-mm-yyyy hh24:mi:ss\')';
        }

		$where[] = ' w.id is not null ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  system_sms_log t '
						. '  inner join ('
						. ' SELECT id, mobile_phone_number, firstname, lastname, parent_id, org, org_name FROM hrs_workers_all '
						. ') w on w.mobile_phone_number = t.log_phone '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.*,'
						. ' w.id, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname,  '
						. ' w.org_name, '
						. ' to_char(t.log_date, \'hh24:mi:ss dd-mm-yyyy\') loging_date '
						. ' from system_sms_log t '
						. '  inner join ('
						. ' SELECT id, mobile_phone_number, firstname, lastname, parent_id, org, org_name FROM hrs_workers_all '
						. ') w on w.mobile_phone_number = t.log_phone '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select '
						. ' k.* '
						. ' from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) k '
						. ' where '
						. ' rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->items = DB::LoadObjectList( $Limit_query );
		return $Return;

	}

}
