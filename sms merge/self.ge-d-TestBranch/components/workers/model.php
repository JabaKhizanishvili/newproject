<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class WorkersModel extends Model
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
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->position = mb_strtolower( trim( Request::getState( $this->_space, 'position', '' ) ) );
		$Return->tablenum = mb_strtolower( trim( Request::getState( $this->_space, 'tablenum', '' ) ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '' ) );
		$Return->category = (int) trim( Request::getState( $this->_space, 'category', '-1' ) );
		$Return->user_role = (int) Request::getState( $this->_space, 'user_role', '-1' );
		$Return->chief = trim( Request::getState( $this->_space, 'chief', '' ) );
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->permit_id = trim( Request::getState( $this->_space, 'permit_id', '' ) );
		$Return->gender = (int) Request::getState( $this->_space, 'gender', '-1' );
		$Return->nationality = (int) Request::getState( $this->_space, 'nationality', '-1' );
		$Return->ldap_username = trim( Request::getState( $this->_space, 'ldap_username', '' ) );
		$Return->accounting_office = (int) Request::getState( $this->_space, 'accounting_office', '0' );
		$Return->counting_type = (int) Request::getState( $this->_space, 'counting_type', '0' );

		$where = array();
		if ( $Return->ldap_username )
		{
			$where[] = ' t.ldap_username like ' . DB::Quote( '%' . $Return->ldap_username . '%' );
		}
		if ( $Return->firstname )
		{
			$where[] = ' t.firstname like ' . DB::Quote( '%' . $Return->firstname . '%' );
		}
		if ( $Return->lastname )
		{
			$where[] = ' t.lastname like ' . DB::Quote( '%' . $Return->lastname . '%' );
		}
		if ( $Return->private_number )
		{
			$where[] = ' t.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}
		if ( $Return->permit_id )
		{
			$where[] = ' t.permit_id like ' . DB::Quote( '%' . $Return->permit_id . '%' );
		}

		if ( $Return->accounting_office > 0 )
		{
			$where[] = ' t.id in( '
							. ' select '
							. ' p.worker '
							. ' from rel_accounting_offices p '
							. ' where '
							. ' p.office =' . DB::Quote( $Return->accounting_office )
							. ' )'
			;
		}
		if ( $Return->position )
		{
			$where[] = ' t.id in( '
							. ' select '
							. ' p.parent_id '
							. ' from hrs_workers_org p '
							. ' where '
							. ' lower(p.position) like ' . DB::Quote( '%' . $Return->position . '%' )
							. ' )'
			;
		}

		if ( !empty( $Return->tablenum ) )
		{
			$where[] = ' t.id in( '
							. ' select '
							. ' p.parent_id '
							. ' from hrs_workers_org p '
							. ' where '
							. ' lower(p.tablenum) like ' . DB::Quote( '%' . $Return->tablenum . '%' )
							. ' )'
			;
		}
		if ( $Return->unit && $Return->org )
		{
			$where[] = ' t.id in( '
							. ' select '
							. ' hwo.parent_id '
							. ' from lib_units s '
							. ' left join hrs_workers_org hwo on hwo .org_place = s.id '
							. ' left join lib_units u on u.lft <= s.lft and u.rgt >= s.rgt and u.id = ' . DB::Quote( $Return->unit )
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null '
							. ' and hwo.id is not null '
							. '  and s.org=  ' . DB::Quote( $Return->org )
							. ')'
			;
		}

		if ( $Return->org )
		{
			$where[] = ' t.id in( '
							. ' select '
							. ' p.parent_id '
							. ' from hrs_workers_org p '
							. ' where '
							. ' p.org = ' . DB::Quote( $Return->org )
							. '   and p.enable = 1 )'
			;
		}

		if ( $Return->chief )
		{
			$Names = explode( ' ', $Return->chief );

			$ChiefWhere = ' t.id in ('
							. ' select '
							. ' rw.worker '
							. ' from REL_WORKER_CHIEF rw '
							. ' where '
							. ' rw.chief in ('
							. ' select '
							. ' id '
							. ' from slf_persons cw '
							. ' where '
			;
			$CWhere = array();
			foreach ( $Names as $Name )
			{
				$CWhere[] = ' (cw.firstname || \' \' || cw.lastname) like ' . DB::Quote( '%' . trim( $Name ) . '%' );
			}
			$ChiefWhere .= implode( ' and ', $CWhere );
			$ChiefWhere .= ' and cw.user_type = 2)) ';
			$where[] = $ChiefWhere;
		}
		if ( $Return->user_role > 0 )
		{
			$where[] = ' t.user_role= ' . DB::Quote( $Return->user_role );
		}
		if ( $Return->category > 0 )
		{
			$where[] = ' t.category_id = ' . DB::Quote( $Return->category );
		}
//		if ( $Return->active == -1 )
//		{
//			$where[] = ' t.active >-1 ';
//		}
//		else
//		{
//			$where[] = ' t.active = ' . DB::Quote( $Return->active );
//		}
		if ( $Return->counting_type > -1 )
		{
			$where[] = ' t.counting_type= ' . DB::Quote( $Return->counting_type );
		}
		if ( $Return->nationality > -1 )
		{
			$where[] = ' t.nationality= ' . DB::Quote( $Return->nationality );
		}
		if ( $Return->gender > -1 )
		{
			$where[] = ' t.gender= ' . DB::Quote( $Return->gender );
		}
		$where[] = 't.id>0 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from slf_persons t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.*, '
						. ' r.LIB_TITLE role '
//						. ' to_char(t.contracts_date, \'dd-mm-yyyy\') s_contracts_date, '
//						. ' to_char(t.contract_end_date, \'dd-mm-yyyy\') s_contract_end_date '
						. ' from slf_persons t '
						. ' left join lib_roles r on r.id = t.user_role '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select k.* '
//						. ' getChiefsByWorker(k.id) all_chiefs '
						. ' from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) k where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;

		$Return->items = DB::LoadObjectList( $Limit_query, 'ID' );
		$Keys = array_keys( $Return->items );
		$ORGData = $this->LoadOrgData( $Keys, $Return );
		foreach ( $Keys as $Key )
		{
			$Return->items[$Key]->ORG = C::_( $Key, $ORGData );
		}
		return $Return;

	}

	public function LoadOrgData( $Keys, $Return )
	{
		$Add = '';
		if ( $Return->org )
		{
			$Add = ' and o.org = ' . DB::Quote( $Return->org );
		}
		$Query = 'select '
						. ' o.id,
       o.parent_id,
       o.org,
       o.enable,
       o.tablenum,
       p.lib_title position,
       o.salary,
       o.contract_state,
       o.user_type,
       o.org_place,
       o.chiefs,
       o.workers,
       o.graphtype,
       o.graphgroup,
       o.structure,
       o.calculus_type,
       o.calculus_date,
       o.calculus_regime,
       o.contracts_date,
       o.contract_end_date,
       o.insurance,
       o.insurance_amount,
       o.work_type,
       o.files,
       o.iban,
       o.salarycomment,
       o.salary_payment_type,
       o.staff_schedule,'
						. ' to_char(o.calculus_date, \'yyyy-mm-dd\') calculus_date,'
						. ' to_char(o.contracts_date, \'yyyy-mm-dd\') contracts_date,'
						. ' to_char(o.contract_end_date, \'yyyy-mm-dd\') contract_end_date,'
						. ' (select '
						. ' max(tt.lib_title) title '
						. ' from LIB_UNITS tt '
						. ' left join lib_unittypes ut on ut.id = tt.type '
						. ' left join lib_units u on u.lft >= tt.lft and u.rgt <= tt.rgt'
						. '  where '
						. ' tt.active > 0 '
						. ' and u.id is not null '
						. ' and ut.def = 1'
						. ' and u.id = ss.org_place '
						. ' and tt.org = o.org '
						. ') ORGPLACE,'
						. ' ( select LISTAGG(t.lib_title, \' / \') WITHIN GROUP (ORDER BY t.lft) from LIB_UNITS t left join lib_unittypes ut on ut.id = t.type left join lib_units u on u.lft >= t.lft and u.rgt <= t.rgt where t.active > 0 and u.id is not null and u.id = ss.org_place '
						. ' and t.org = o.org '
						. ' ) orgpath '
						. ' from HRS_WORKERS_ORG o '
						. ' left join lib_staff_schedules ss on ss.id = o.staff_schedule  '
						. ' left join lib_positions p on p.id = ss.position  '
						. ' left join lib_unitorgs uo on uo.id = o.org '
						. ' where '
						. ' o.enable =  1 '
						. '  and uo.active = 1 '
						. ' and o.parent_id in (' . implode( ',', $Keys ) . ' ) '
						. $Add
						. ' order by o.id '
		;
		$Data = DB::LoadObjectList( $Query );
		$OrgData = array();
		foreach ( $Data as $D )
		{
			$ID = C::_( 'PARENT_ID', $D );
			$OrgData[$ID] = C::_( $ID, $OrgData, array() );
			$OrgData[$ID][] = $D;
		}
		return $OrgData;

	}

}
