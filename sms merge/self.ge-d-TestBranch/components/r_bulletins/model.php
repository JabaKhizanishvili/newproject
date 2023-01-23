<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class R_BulletinsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->sphere = (int) trim( Request::getState( $this->_space, 'sphere', '' ) );
		$Return->department = (int) trim( Request::getState( $this->_space, 'department', '' ) );
		$Return->chapter = (int) trim( Request::getState( $this->_space, 'chapter', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->state = trim( Request::getState( $this->_space, 'state', -1 ) );
//		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
//		$order = $Return->order;
		$order_by = ' order by worker asc, t.start_date desc ';

		$where = array();
//		switch ( $Return->state )
//		{
//			case 0:
//				$where[] = 't.status=0 ';
//				break;
//			case 1:
//				$where[] = 't.status=1 ';
//				break;
//			case 2:
//				$where[] = 't.status=2 ';
//				break;
//			default:
		$where[] = 't.status >0 ';
//				break;
//		}

		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->sphere )
		{
			$where[] = ' w.sphere= ' . $Return->sphere;
		}
		if ( $Return->department )
		{
			$where[] = ' w.department= ' . $Return->department;
		}
		if ( $Return->chapter )
		{
			$where[] = ' w.chapter= ' . $Return->chapter;
		}
		if ( Xhelp::checkDate( $Return->start_date ) )
		{
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' t.start_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}
		if ( Xhelp::checkDate( $Return->end_date ) )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.start_date < to_date(\'' . $EndDate->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}
		if ( count( $where ) > 0 )
		{
			$where[] = 't.type = ' . APP_BULLETINS;
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$countQuery = 'select count(*) from hrs_applications t '
							. ' left join hrs_workers w on w.id = t.worker'
							. ' left join slf_persons app on app.id = t.approve'
							. $whereQ
			;
			$Return->total = DB::LoadResult( $countQuery );
			$Query = 'select t.id,'
							. ' w.id userid, '
							. ' t.type, '
							. ' to_char(t.start_date, \'dd-mm-yyyy\') start_date, '
							. ' to_char(t.end_date, \'dd-mm-yyyy\') end_date, '
							. ' to_char(t.rec_date, \'dd-mm-yyyy hh24:mi\') rec_date, '
							. ' t.day_count, '
//							. ' d.lib_title department, '
//							. ' s.lib_title section, '
							. ' t.status, '
							. ' t.ucomment, '
							. ' t.approve, '
							. ' to_char(t.approve_date, \'dd-mm-yyyy hh24:mi:ss\') approve_date, '
							. 'w.firstname||\' \'|| w.lastname worker,  '
							. 'app.firstname||\' \'|| app.lastname approver '
							. ' from hrs_applications t '
							. ' left join hrs_workers w on w.id = t.worker'
							. ' left join slf_persons app on app.id = t.approve'
//							. ' left join lib_sections s on s.id=w.section_id '
//							. ' left join lib_departments d on d.id = w.dept_id '
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
		}
		else
		{
			$Return->items = array();
		}
		return $Return;

	}

}
