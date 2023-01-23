<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class calendarModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();

//		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
//		$order = $Return->order;

		$Return->date = trim( Request::getState( $this->_space, 'date', PDate::Get()->toFormat( '%Y-%m' ) ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );
        $Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
        $Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
        $Return->org = trim( Request::getState( $this->_space, 'org', '' ) );
        $Return->org_place = trim( Request::getState( $this->_space, 'org_place', '' ) );

//		$where = array();
//
//		if ( $Return->active > -1 )
//		{
//			$where[] = ' t.active= ' . $Return->active;
//		}
//		else
//		{
//			$where[] = 't.active >-1 ';
//		}
//		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
//		$countQuery = 'select count(*) from lib_applications_types t '
//						. $whereQ
//		;
//		$Return->total = DB::LoadResult( $countQuery );
//		$Query = 'select '
//						. ' t.* '
//						. ' from lib_applications_types t '
//						. $whereQ
//						. $order_by
//		;
//		$Limit_query = 'select * from ( '
//						. ' select a.*, rownum rn from (' .
//						$Query
//						. ') a) where rn > '
//						. $Return->start
//						. ' and rn <= ' . $Return->limit;
//		$Return->items = DB::LoadObjectList( $Limit_query );
		return $Return;

	}

	public function GetStartEnd( $year, $week )
	{
		$dateTime = new DateTime();
		$dateTime->setISODate( $year, $week );
		$result['START'] = $dateTime->format( 'd-m-Y' );
		$dateTime->modify( '+6 days' );
		$result['END'] = $dateTime->format( 'd-m-Y' );
		return $result;

	}

	public function getWorkerHolidays( $startDate, $EndDate )
	{
        $Return = $this->getReturn();
        $Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
        $Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
        $Return->org = trim( Request::getState( $this->_space, 'org', '' ) );
        $Return->org_place = trim( Request::getState( $this->_space, 'org_place', '' ) );

        $where = array();

        if ($Return->firstname) {
            $where[] = ' w.person in (' . $this->_search( $Return->firstname, 'FIRSTNAME', 'slf_persons' ) . ')';
        }

        if ($Return->lastname) {
            $where[] = ' w.person in (' . $this->_search( $Return->lastname, 'LASTNAME', 'slf_persons' ) . ')';
        }

        if ($Return->org) {
            $where[] = ' a.org = ' . $Return->org;
        }

        if ( $Return->org_place ) {
            $where[] = ' 
                a.worker in (
                select ww.orgpid from hrs_workers_sch ww where 
                ww.org_place in (select t.id from lib_units t left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . DB::Quote( $Return->org_place ) . ' where t.active = 1 and u.id is not null) 
                and ww.active > 0
                )
            ';
        }

//        if ( $Return->org_place > 0 ) {
//            $where[] = ' lu2.id not in( '
//                . ' select '
//                . ' t.id '
//                . ' from lib_units t '
//                . ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . $Return->org_place
//                . ' where '
//                . ' t.active = 1 '
//                . ' and u.id is not null ) '
//            ;
//        }

		$Start = ' to_date(' . DB::Quote( $startDate->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\') ';
		$End = ' to_date(' . DB::Quote( $EndDate->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\') ';

        $where[] = ' a.status > 0
   and (a.type in (5) OR a.TYPE IN (SELECT LLAT.ID  FROM LIB_LIMIT_APP_TYPES llat WHERE LLAT.ACTIVE = 1))
   and ( '
            . $Start . ' between a.start_date and a.end_date '
            . ' or ' . $End . ' between a.start_date and a.end_date '
            . ' or a.start_date between ' . $Start . ' and ' . $End
            . '  )'
            . ' and w.active = 1 ';

        $whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$Query = 'select distinct * from( select NVL(ap.lib_title, \'შვებულება\') lib_title,
       sp.firstname || \' \' || sp.lastname worker,
       lu.LIB_TITLE org_name,
       to_char(a.start_date, \'yyyy-mm-dd\') start_date,
       to_char(a.end_date, \'yyyy-mm-dd\') end_date
  from hrs_applications a
  left join rel_person_org w
    on w.id = a.worker
  left join lib_units lu2 on lu2.org = w.org
  left join lib_applications_types ap
    on ap.type = a.type
  left join slf_persons sp on w.person = sp.id
  left join lib_unitorgs lu on w.org = lu.id
  LEFT JOIN LIB_LIMIT_APP_TYPES llat2 ON LLAT2.id = a.type
 ' . $whereQ
						. ' order by ap.lib_title, LLAT2.lib_title )'
		;
		$data = DB::LoadObjectList( $Query );
		$Return = array();
		foreach ( $data as $D )
		{
			$Days = Helper::GetDays( C::_( 'START_DATE', $D ), C::_( 'END_DATE', $D ) );
			foreach ( $Days as $Day )
			{
				$Return[$Day] = C::_( $Day, $Return, array() );
				$Return[$Day][] = $D;
			}
		}
		return $Return;

	}

}
