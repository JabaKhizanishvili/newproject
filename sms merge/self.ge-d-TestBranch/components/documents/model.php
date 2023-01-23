<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class DocumentsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );
		$Return->lib_desc = trim( Request::getState( $this->_space, 'lib_desc', '' ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );
		$Return->worker = trim( Request::getState( $this->_space, 'worker', '' ) );
		$Return->must = trim( Request::getState( $this->_space, 'must', '-1' ) );

		$where = array();
		if ( $Return->lib_title )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->lib_title, [ 'lib_title' ], 'lib_documents_uploads' ) . ')';
		}
		if ( $Return->worker )
		{
			$where[] = ' t.id in (select r.doc_id from rel_documents_uploads r where r.worker in (select id from slf_persons where id in (' . $this->_search( $Return->worker, [ 'firstname', 'lastname' ], 'slf_persons' ) . ')))';
		}

		if ( $Return->must > -1 )
		{
			$where[] = ' t.must= ' . $Return->must;
		}

		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . $Return->active;
		}
		else
		{
			$where[] = 't.active >-1 ';
		}
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$countQuery = 'select count(*) from  lib_documents_uploads t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from lib_documents_uploads t '
//                        . ' left join lib_documents_uploads p on p.id = t.worker'
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
		return $Return;

	}

}
