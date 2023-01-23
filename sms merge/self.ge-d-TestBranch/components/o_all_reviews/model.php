<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class o_all_reviewsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$order_by = ' order by worker asc, t.event_date asc ';
		$where = array();
		$where[] = ' w.active > -1';
		$where[] = ' t.u_comment is not null ';
		$where[] = ' t.c_resolution = 0 ';

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$Return->total = 0;
		$Query = 'select '
						. ' t.*, '
						. ' to_char(t.event_date, \'dd-mm-yyyy hh24:mi:ss\') event_date, '
						. ' to_char(t.u_comment_date, \'dd-mm-yyyy hh24:mi:ss\') u_comment_date, '
						. ' w.id userid, '
						. ' a.lib_title event_name, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname '
						. ' from HRS_STAFF_EVENTS t '
						. ' left join lib_actions a on a.type = t.real_type_id '
						. ' left join slf_persons w on w.id = t.staff_id '
						. $whereQ
						. $order_by
		;
		$Data = DB::LoadObjectList( $Query );
		$Return->items = array();
		foreach ( $Data as $Item )
		{
			$UserID = C::_( 'USERID', $Item );
			$Return->items[$UserID] = C::_( $UserID, $Return->items, array() );
			$Return->items[$UserID][] = $Item;
		}
		return $Return;

	}

	public function SaveData( $Data )
	{
		foreach ( $Data as $key => $value )
		{
			$Key = trim( $key );
			if ( empty( $Key ) )
			{
				continue;
			}
			$C_RESOLUTION = intval( C::_( 'C_RESOLUTION', $value ) );
			$C_COMMENT = trim( C::_( 'C_COMMENT', $value ) );
			if ( empty( $C_RESOLUTION ) )
			{
				continue;
			}
			if ( $C_RESOLUTION == 2 and empty( $C_COMMENT ) )
			{
				continue;
			}
			$C_COMMENT_DATE = PDate::Get()->toFormat();
			$Query = 'update '
							. ' hrs_staff_events e '
							. ' set '
							. ' e.c_resolution = ' . DB::Quote( $C_RESOLUTION ) . ','
							. ' e.c_comment = ' . DB::Quote( $C_COMMENT ) . ','
							. ' e.c_chief = ' . DB::Quote( Users::GetUserID() ) . ','
							. ' e.c_comment_date = to_date(' . DB::Quote( $C_COMMENT_DATE ) . ', ' . DB::Quote( 'yyyy-mm-dd hh24:mi:ss' ) . ' ) '
							. ' where id = ' . DB::Quote( $Key )
//							. ' and staff_id = ' . DB::Quote( Users::GetUserID() )
			;
			DB::Update( $Query );
		}

	}

}
