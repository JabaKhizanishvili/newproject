<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_sms_serviceModel extends Model
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
		$order_by = ' order by ' . $order . ' ' . $dir;

		$countQuery = 'select count(*) from  hrs_sms_log t '
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.*,'
						. ' substr(t.log_from, -9) log_phone, '
						. ' to_char(t.log_date, \'hh24:mi:ss dd-mm-yyyy\') loging_date '
						. ' from hrs_sms_log t '
						. $order_by
		;
		$Limit_query = 'select '
						. ' k.*, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname  '
						. ' from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) k'
						. '  left join slf_persons w on w.mobile_phone_number = k.log_phone '
						. ' where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;

		$Return->items = DB::LoadObjectList( $Limit_query );
		return $Return;

	}

}
