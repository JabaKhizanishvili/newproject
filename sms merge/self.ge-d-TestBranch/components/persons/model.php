<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class PersonsModel extends Model
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
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->ldap_username = trim( Request::getState( $this->_space, 'ldap_username', '' ) );
		$Return->permit_id = trim( Request::getState( $this->_space, 'permit_id', '' ) );
		$Return->gender = (int) trim( Request::getState( $this->_space, 'gender', '-1' ) );
		$Return->nationality = (int) trim( Request::getState( $this->_space, 'nationality', '-1' ) );
		$Return->accounting_offices = (int) trim( Request::getState( $this->_space, 'accounting_offices', '0' ) );
		$Return->counting_type = (int) trim( Request::getState( $this->_space, 'counting_type', '-1' ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '1' ) );
		$Return->userrole = (int) trim( Request::getState( $this->_space, 'userrole', '0' ) );
		$Return->pay_pension = (int) trim( Request::getState( $this->_space, 'pay_pension', '-1' ) );
        $attributes = array_filter(Request::getState( $this->_space, 'attributes', [] ), function ($i) {
            return !empty($i);
        });
		$Return->attributes = implode(',', $attributes);

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->firstname, 'FIRSTNAME', 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->lastname, 'LASTNAME', 'slf_persons' ) . ')';
		}
        if (!empty($Return->attributes)) {
            $where[] = ' t.id in ( 
                    SELECT 
                         ra.item_id
                    FROM rel_attributes ra
                    left join lib_attributes la on la.id = ra.attribute_id
                    where 
                        ra.item_type = 1
                    and
                        la.active = 1
                    and 
                        la.id in ( ' . $Return->attributes . ')
                ) ';
        }
		if ( $Return->private_number != '' )
		{
			$where[] = ' t.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}
		if ( $Return->ldap_username )
		{
			$where[] = ' t.ldap_username like ' . DB::Quote( '%' . $Return->ldap_username . '%' );
		}
		if ( $Return->permit_id != '' )
		{
			$where[] = ' t.permit_id like ' . DB::Quote( '%' . $Return->permit_id . '%' );
		}
		if ( $Return->userrole > 0 )
		{
			$where[] = ' t.user_role= ' . DB::Quote( $Return->userrole );
		}
		if ( $Return->pay_pension > -1 )
		{
			$where[] = ' t.pay_pension= ' . DB::Quote( $Return->pay_pension );
		}

		if ( $Return->gender > -1 )
		{
			$where[] = ' t.gender= ' . DB::Quote( $Return->gender );
		}
		if ( $Return->nationality > -1 )
		{
			$where[] = ' t.nationality= ' . DB::Quote( $Return->nationality );
		}
		if ( $Return->accounting_offices > 0 )
		{
			$where[] = ' t.id in (select  dd.person from slf_worker dd where  dd.id in (select ff.worker from rel_accounting_offices ff where ff.office = ' . DB::Quote( $Return->accounting_offices ) . ' ))';
		}
		if ( $Return->counting_type > -1 )
		{
			$where[] = ' t.counting_type= ' . DB::Quote( $Return->counting_type );
		}
		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . DB::Quote( $Return->active );
		}
		else
		{
			$where[] = ' t.active=1 ';
		}

		$where[] = 't.id > 0 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  slf_persons t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from slf_persons t '
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
