<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class newsitemModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		$ID = Request::getInt( 'id' );
		if ( empty( $ID ) )
		{
			XError::setError( 'News Id Not Defined!' );
			Users::Redirect( '/' );
		}

		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->text = trim( Request::getState( $this->_space, 'text', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		$where[] = 't.active >-1 ';
		$where[] = 't.id<> ' . $ID;

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$Return->total = 0; //DB::LoadResult( $countQuery );
		$Query = 'select t.*, '
						. ' to_char(t.publish_date, \'dd-mm-yyyy\') v_publish_date, '
						. ' to_char(t.unpublish_date, \'dd-mm-yyyy\') v_unpublish_date '
						. ' from news t '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn >0  and rn <=  5 ';

		$Return->items = DB::LoadObjectList( $Limit_query );
		return $Return;

	}

	public function getItem()
	{

		$ID = Request::getInt( 'id' );
		if ( empty( $ID ) )
		{
			XError::setError( 'News Id Not Defined!' );
			Users::Redirect( '/' );
		}

		$query = 'select '
						. 't.*,'
						. ' to_char(t.publish_date, \'dd-mm-yyyy\') v_publish_date '
//						. ' t.firstname, '
//						. ' t.lastname, '
//						. ' t.birthdate, '
//						. ' t.photo, '
//						. ' t.position,'
//						. ' to_char(t.birthdate, \'mm-dd\') k '
						. ' from news t '
						. ' where '
						. ' t.active = 1 '
						. ' and t.id =  ' . $ID
//						. ' and sysdate between t.publish_date and t.unpublish_date '
//						. ' order by t.publish_date desc'

		;

		$Data = DB::LoadObject( $query );
		return $Data;

	}

}
