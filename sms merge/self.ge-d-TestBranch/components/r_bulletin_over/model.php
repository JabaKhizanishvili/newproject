<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_bulletin_overModel extends Model
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
		$Return->day = (int) trim( Request::getState( $this->_space, 'day', '' ) );
		$Return->sphere = (int) trim( Request::getState( $this->_space, 'sphere', '' ) );
		$Return->department = (int) trim( Request::getState( $this->_space, 'department', '' ) );
		$Return->chapter = (int) trim( Request::getState( $this->_space, 'chapter', '' ) );
		$Return->cat_id = (int) trim( Request::getState( $this->_space, 'cat_id', '' ) );
		$order_by = ' order by diff desc ';
		$where = array();
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
		if ( $Return->cat_id > 0 )
		{
			$where[] = ' w.category_id= ' . $Return->cat_id;
		}
		if ( $Return->day > 0 )
		{
			$where[] = ' (trunc(sysdate) - trunc(t.start_date)) >= ' . $Return->day;
		}
		if ( count( $where ) > 0 )
		{
			$where[] = ' w.active > -1';
			$where[] = ' t.status = 1 ';
			$where[] = ' t.type = ' . APP_BULLETINS;
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$Return->total = 0;
			$Query = ' select '
							. ' w.firstname, '
							. ' w.lastname,'
							. ' t.*, '
							. ' to_char(t.start_date, \'dd-mm-yyyy\') start_date, '
							. ' (trunc(sysdate) - trunc(t.start_date)) diff '
							. ' from HRS_APPLICATIONS t '
							. ' left join hrs_workers w on w.id = t.worker'
							. $whereQ
							. $order_by
			;
			$Return->items = DB::LoadObjectList( $Query );
		}
		else
		{
			$Return->items = array();
		}

		return $Return;

	}

}
