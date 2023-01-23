<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_unknown_cardsModel extends Model
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
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$where = array();
		if ( Xhelp::checkDate( $Return->start_date ) )
		{
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' t.rec_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}
		if ( Xhelp::checkDate( $Return->end_date ) )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.rec_date < to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
		}
		if ( count( $where ) > 0 )
		{
			$where[] = ' w.id is null or d.id is null ';
			$where[] = ' t.card_id is not null ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$Return->total = 0;
			$Query = ' select '
							. ' t.*, '
							. ' d.lib_title door, '
							. ' d.type door_type, '
							. ' w.firstname wfirstname, '
							. ' w.lastname wlastname  '
							. ' from hrs_transported_data t '
							. ' left join lib_doors d  on d.code = t.access_point_code and d.active=1 '
							. ' left join rel_person_permit pp  on t.card_id = pp.permit_id '
							. ' left join slf_persons w  on w.id = pp.person '
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
