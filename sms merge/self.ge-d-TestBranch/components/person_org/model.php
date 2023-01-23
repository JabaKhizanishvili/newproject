<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';
require_once 'changes.php';

class Person_orgModel extends Model
{
	protected $OrgTable = null;
	protected $Table = null;
	protected $ChangesTable = null;
	protected $ldap = null;

	public function __construct( $params )
	{
		$this->Table = new Person_orgTable( );
		$this->ChangesTable = new ChangesTable( );
		parent::__construct( $params );

	}

	public function getItem( $id = null )
	{
		$data = Request::getVar( 'params', array() );
		if ( empty( $id ) )
		{
			$id = C::_( 0, Request::getVar( 'nid', array() ) );
		}

		if ( isset( $id ) && !empty( $id ) )
		{
			$this->Table->load( $id );
			$this->Table->ACCOUNTING_OFFICE = implode( '|', $this->getAccountingOffices() );
		}
		else
		{
			$this->Table->ORG = C::_( 'ORG', $data );
		}
		$this->Table->PERSON = C::_( 'PERSON', $data );

		return $this->Table;

	}

	public function getCurrentItem( $id = 0 )
	{
		return Rollback::getCurrentItem( $id );

	}

	public function getPreviousItem( $id = 0, $equals = false, $not_id = false )
	{
		return Rollback::getPreviousItem( $id, $equals, $not_id );

	}

	public function getItems( $id = 0 )
	{
		$data = Request::getVar( 'params', array() );
		if ( empty( $id ) )
		{
			$id = Request::getVar( 'nid', array() );
		}
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			foreach ( $id as $id_ )
			{
				$this->Table->resetAll();
				$this->Table->load( $id_ );
				if ( C::_( 'ACTIVE', $this->Table ) < 0 )
				{
					XError::setError( 'you cannot change data of inactive worker!' );
					return false;
				}
			}
		}
		return $this->Table;

	}

//	Assignment ____________________________________________
	public function save_assignment( $data )
	{
		$done = false;
		$date = '';
		$prepare = (array) $this->collect_assignment( $data );
		foreach ( $prepare as $dat )
		{
			if ( $dat->store() )
			{
				$done = true;
				if ( $date == '' )
				{
					$date = C::_( 'CHANGE_DATE', $dat );
				}
			}
		}
		if ( $done == true )
		{
			Job::Assignment( $prepare, $date );
		}
		return $done;

	}

	/**
	 * 
	 * @param type $data
	 * @return array
	 */
	public function collect_assignment( $data )
	{
		$sch = C::_( 'STAFF_SCHEDULE', $data, 0 );
		if ( $sch > 0 && C::_( 'SALARY_ONOFF', $data ) == 1 )
		{
			$data['SALARY'] = $this->get_schedule_salary( $sch );
		}
		else
		{
			$data['SALARY'] = (float) C::_( 'SALARY', $data, 0 );
		}
		unset( $data['SALARY_ONOFF'] );

//		if ( isset( $data['BENEFIT_TYPES'] ) && is_array( $data['BENEFIT_TYPES'] ) )
//		{
//			$data['BENEFIT_TYPES'] = implode( ',', C::_( 'BENEFIT_TYPES', $data ) );
//		}
		$EACH = [];
		if ( empty( C::_( 'PERSON', $data ) ) )
		{
			return $EACH;
		}

		$start = PDate::Get( C::_( 'CONTRACTS_DATE', $data ) );
		$end = PDate::Get( C::_( 'CONTRACT_END_DATE', $data ) );
		if ( $start->toUnix() >= $end->toUnix() )
		{
			return $EACH;
		}

		$workers = helper::CleanArray( explode( ',', C::_( 'PERSON', $data ) ) );
		foreach ( $workers as $worker )
		{
			$TBL = clone $this->Collector( $data, $worker, 1 );
			$TBL->SALARY = (float) C::_( 'SALARY', $data, 0 );
			if ( !$TBL->check() )
			{
				return $EACH;
			}
			$CheckWorker = $this->CheckWorker( $TBL );
			if ( $CheckWorker )
			{
				XError::setError( 'Worker with this data already exists!' );
				return $EACH;
			}
			$CheckProcess = $this->CheckProcess( $TBL );
			if ( $CheckProcess )
			{
				XError::setError( 'Process with this data already exists!' );
				return $EACH;
			}

			$EACH[$worker] = $TBL;
		}

		if ( !count( $EACH ) )
		{
			return $EACH;
		}
		return $EACH;

	}

//	Save Rollback _______________________________________________
	public function save_rollback( $data )
	{
		return Rollback::save_rollback( $data );

	}

	public function collect_rollback( $worker = 0, $change_type = 0, $data = [], $date = null, $params = null )
	{
		return Rollback::collect_rollback( $worker, $change_type, $data, $date, $params );

	}

//	Change _______________________________________________
	public function save_changing( $data )
	{
		$done = false;
		$date = '';
		$prepare = $this->collect_changes( $data );
		foreach ( $prepare as $dat )
		{
			if ( $dat->store() )
			{
				$done = true;
				if ( $date == '' )
				{
					$date = C::_( 'CHANGE_DATE', $dat );
				}
			}
		}
		Job::Change( $prepare, $date );
		return $done;

	}

//	Benefits _______________________________________________
	public function save_benefits( $data )
	{
		$action = C::_( 'CHANGE_SUB_TYPE', $data );
		if ( !$this->checkBenefitData( C::_( 'BENEFIT_TYPES', $data ), $action, $data ) )
		{
			return false;
		}

		if ( !Benefits::register_benefits( $data ) )
		{
			return false;
		}

		Job::Change();
		return true;

	}

	public function checkBenefitData( $benefits = [], $action = 0, &$data )
	{
		$query = 'select t.id, t.identifier, t.benefit_end_date from lib_f_benefits t where t.active = 1';
		$Categories = DB::LoadobjectList( $query, 'ID' );
		$assignment_hour = (int) Helper::GetConfig( 'assignment_hour', 13 );

		$collect = [];
		foreach ( $benefits as $k => $DATA )
		{
			foreach ( $DATA as $kk => $Data )
			{
				if ( $action == 3 && !isset( $Data['DELETE'] ) )
				{
					continue;
				}

				$benefit = C::_( 'BENEFIT', $Data );
				if ( (int) $benefit < 0 )
				{
					return false;
				}

				$bens = Benefits::get_benefit_types( (int) C::_( 1, explode( '|', $benefit ) ) );
				$ben_start = C::_( 'START_DATE', $bens );
				$ben_end = C::_( 'END_DATE', $bens );
				$change_date = C::_( 'CHANGE_DATE', $Data );
				if ( $action == 3 && isset( $Data['DELETE'] ) && empty( $change_date ) )
				{
					return false;
				}

				if ( empty( $change_date ) )
				{
					return false;
				}

				$cat = (int) C::_( 0, explode( '|', $benefit ) );
				$category = C::_( $cat, $Categories );
				if ( $action != 3 && C::_( 'IDENTIFIER', $category ) == 1 && empty( C::_( 'IDENTIFIER', $Data ) ) )
				{
					return false;
				}

				if ( $action != 3 && C::_( 'BENEFIT_END_DATE', $category ) == 1 && empty( C::_( 'BENEFIT_END_DATE', $Data ) ) )
				{
					return false;
				}

				if ( $action != 3 && C::_( 'BENEFIT_END_DATE', $category ) != 1 && empty( C::_( 'BENEFIT_END_DATE', $Data ) ) )
				{
					$Data['BENEFIT_END_DATE'] = $ben_end;
				}

				if ( PDate::Get( C::_( 'CHANGE_DATE', $Data ) )->toUnix() < PDate::Get( $ben_start )->toUnix() )
				{
					XError::setError( 'start date not allowed!' );
					return false;
				}

				if ( PDate::Get( C::_( 'BENEFIT_END_DATE', $Data ) )->toUnix() > PDate::Get( $ben_end )->toUnix() )
				{
					XError::setError( 'end date not allowed!' );
					return false;
				}

				$Data['CHANGE_DATE'] = PDate::Get( $Data['CHANGE_DATE'] )->toFormat( '%Y-%m-%d ' . ($assignment_hour == 0 ? '00' : $assignment_hour) . ':00:00' );
				$Data['BENEFIT_END_DATE'] = PDate::Get( $Data['BENEFIT_END_DATE'] )->toFormat( '%Y-%m-%d ' . ($assignment_hour <= 1 ? 23 : $assignment_hour - 1) . ':59:59' );

				$collect[$k][$kk] = $Data;
			}
		}

		if ( !empty( $data ) )
		{
			$data['BENEFIT_TYPES'] = $collect;
		}

		return true;

	}

//	Schedule Change _______________________________________________
	public function save_schedulechanging( $data )
	{
		if ( empty( $data['STAFF_SCHEDULE'] ) )
		{
			return false;
		}

        $sch = C::_( 'STAFF_SCHEDULE', $data, 0 );
        if ( $sch > 0 && C::_( 'SALARY_ONOFF', $data ) == 1 ) {
            $data['SALARY'] = $this->get_schedule_salary( $sch );
        } else {
            $data['SALARY'] = (int) C::_( 'SALARY', $data, 0 );
        }

        unset( $data['SALARY_ONOFF'] );

		$worker = C::_( 'WORKERS', $data );
		if ( $this->check_same_schedule( $worker, C::_( 'STAFF_SCHEDULE', $data ) ) )
		{
			Xerror::setError( 'selected schedule is already used!' );
			return false;
		}

		$datt = $data;
		$datt['PERSON'] = C::_( 'PERSON', Xhelp::getWorker_sch( $worker ) );
		if ( $this->CheckWorker( $datt ) )
		{
			XError::setError( 'Worker with this data already exists!' );
			return false;
		}

		if ( $this->CheckWorker( $datt ) )
		{
			XError::setError( 'Worker with this data already exists!' );
			return false;
		}

		$done = false;
		$date = '';
		$token = md5( time() );

		$this->Table->resetAll();
		$this->Table->load( $worker );
		$rame = Xhelp::bind( $data, (array) $this->Table );
		$dat1 = $this->collect_changes( $rame, 7 )[$worker];
		$dat2 = $this->collect_changes( $data, 5 )[$worker];
		if ( empty( $dat1 ) || empty( $dat2 ) )
		{
			return false;
		}

		$dat2->CHANGE_DATE = PDate::Get( C::_( 'CHANGE_DATE', $dat1 ) . ' + 10 minutes' )->toFormat();
		$dat1->TOKEN = $token;
		$dat2->TOKEN = $token;
		$prepare[] = $dat1;
		$prepare[] = $dat2;
		foreach ( $prepare as $dat )
		{
			if ( $dat->store() )
			{
				$done = true;
				if ( $date == '' )
				{
					$date = C::_( 'CHANGE_DATE', $dat );
				}
			}
		}

		Job::ScheduleChange( $prepare, $date );
		return $done;

	}

	public function collect_changes( $data, $change_type = 2 )
	{
//		if ( isset( $data['BENEFIT_TYPES'] ) && is_array( $data['BENEFIT_TYPES'] ) )
//		{
//			$data['BENEFIT_TYPES'] = implode( ',', C::_( 'BENEFIT_TYPES', $data ) );
//		}
		if ( empty( C::_( 'CHANGE_DATE', $data ) ) )
		{
			return false;
		}

		if ( empty( C::_( 'WORKERS', $data ) ) )
		{
			return false;
		}

        if (is_array($data['ATTRIBUTES'])) {
            $data['ATTRIBUTES'] = array_filter($data['ATTRIBUTES'], function ($i) {
                return !empty($i);
            });

            $data['ATTRIBUTES'] = implode(',', $data['ATTRIBUTES']);
        }

		$start = PDate::Get( C::_( 'CONTRACTS_DATE', $data ) );
		$end = PDate::Get( C::_( 'CONTRACT_END_DATE', $data ) );
		if ( $start->toUnix() >= $end->toUnix() )
		{
			return false;
		}

		$Workers = explode( ',', C::_( 'WORKERS', $data ) );
		$Slf_workerTable = new TableSlf_workerInterface( 'slf_worker', 'ID' );
		$checker = clone $Slf_workerTable;
		$collect = [];
		foreach ( $Workers as $worker )
		{
			$PD_date = PDate::Get( C::_( 'CHANGE_DATE', $data ) )->toFormat( '%Y-%m-%d' );
			if ( !$this->check_proccess_date( $worker, $PD_date ) )
			{
				XError::setError( 'can not register proccess with date, that less then the last record date!' );
				return false;
			}

			$next_date = Job::next_change_date( $worker, $PD_date );
			if ( $next_date )
			{
				$data['CHANGE_DATE'] = $next_date;
			}

			$CheckIfWorker = $this->CheckWorker( $data );
			if ( $CheckIfWorker && $CheckIfWorker != $worker )
			{
				XError::setError( 'Worker with this data already exists!' );
				return false;
			}

			$CheckIfProcess = $this->CheckProcess( $data, 3, 0 );
			if ( $CheckIfProcess )
			{
				XError::setError( 'Process with this data already exists as release!' );
				return false;
			}

			$wdata = $this->getItem( $worker );
			$sch = C::_( 'STAFF_SCHEDULE', $wdata, 0 );
			if ( $sch > 0 && C::_( 'SALARY_ONOFF', $data ) == 1 )
			{
				$data['SALARY'] = $this->get_schedule_salary( $sch );
			}
			else
			{
				$data['SALARY'] = (float) C::_( 'SALARY', $data, 0 );
			}
			unset( $data['SALARY_ONOFF'] );

			$this->Table->resetAll();
			$Base = clone $this->Table;
			$CH_Clone = clone $this->Table;

			$Query = 'select x.* from slf_worker x where '
							. ' x.id = ' . DB::Quote( $worker )
							. ' and x.active = 1';
			$dat = DB::LoadObject( $Query );
			$Base->bind( $dat );

			if ( !$CH_Clone->bind( $data ) )
			{
				return false;
			}

			$Result = [];
			foreach ( $CH_Clone as $key => $value )
			{
				if ( isset( $value ) && $Base->$key != $value )
				{
					$Result[$key] = $value;
				}
			}
			unset( $Result['PERSON'] );

			$ACCOUNTING_OFFICE = Helper::CleanArray( C::_( 'ACCOUNTING_OFFICES', $Result, array() ), 'Int' );

			asort( $ACCOUNTING_OFFICE );

			if ( !count( $ACCOUNTING_OFFICE ) )
			{
//				unset( $Result['ACCOUNTING_OFFICES'] );
				$Result['ACCOUNTING_OFFICES'] = C::_( 'ACCOUNTING_OFFICES', $CH_Clone );
			}
			else
			{
				$Result['ACCOUNTING_OFFICES'] = implode( ',', $ACCOUNTING_OFFICE );
			}

			if ( count( $Result ) > 0 )
			{
				$this->ChangesTable->resetAll();
				if ( !$this->ChangesTable->bind( $Base ) )
				{
					return false;
				}

				$this->ChangesTable->ID = '';
				$this->ChangesTable->WORKER_ID = $worker;
				$this->ChangesTable->CHANGE_TYPE = $change_type;
				$this->ChangesTable->CHANGE_DATE = C::_( 'CHANGE_DATE', $data );
				$this->ChangesTable->CHANGE_SUB_TYPE = C::_( 'CHANGE_SUB_TYPE', $data );
				$this->ChangesTable->STATUS = 0;
				$this->ChangesTable->SALARY = (float) C::_( 'SALARY', $data, 0 );
				$this->ChangesTable->CREATE_DATE = PDate::Get()->toFormat();
				$this->ChangesTable->CREATOR_PERSON = Users::GetUserID();
                $this->ChangesTable->ATTRIBUTES = $data['ATTRIBUTES'];
				$this->ChangesTable->bind( $Result );

				if ( !$this->ChangesTable->checkChanges() )
				{
					return false;
				}
				$collect[$worker] = clone $this->ChangesTable;
			}
			else
			{
				XError::setError( 'Changes not detected!' );
				return false;
			}
		}
		return $collect;

	}

//	Release  _______________________________________________
	public function collect_release( $data )
	{
		$W = explode( ',', C::_( 'WORKERS', $data ) );
		if ( $W )
		{
			$wIn = [];
			$depts = Xhelp::getWorkerData( C::_( 'WORKERS', $data ) );
			if ( $depts )
			{
				unset( $data['CONFIRMATION'] );
				unset( $data['CONFIRMATIONLABEL'] );
				foreach ( $depts as $each )
				{
					$wIn[C::_( 'ID', $each )] = $each;
				}
			}
			$collect = [];
			foreach ( $W as $w )
			{
				$wData = C::_( $w, $wIn );
				$F = [];
				$F['PERSON'] = C::_( 'PERSON', $wData );
				$F['STAFF_SCHEDULE'] = C::_( 'STAFF_SCHEDULE', $wData );
				$collect['DATA'][$w] = array_merge( $F, $data );
			}
			$collect['DISPLAY_CUT'] = [
					'WORKERS',
			];
			$collect['TRANSPORT_CUT'] = [
					'PERSON',
					'STAFF_SCHEDULE',
			];
			return $collect;
		}

	}

	public function save_release( $data )
	{
		$data['RELEASE_FILES'] = explode( ',', C::_( 'RELEASE_FILES', $data ) );

		$AUTO_PERSON_STATUS_STOP = array();
		if ( C::_( 'EXTRA_PARAMS', $data ) == '1' )
		{
			$AUTO_PERSON_STATUS_STOP = C::_( 'AUTO_PERSON_STATUS_STOP', $data, array() );
			$aCount = count( $AUTO_PERSON_STATUS_STOP );
			$bCount = C::_( 'AUTO_PERSON_STATUS_STOP_COUNT', $data, null );
			if ( $aCount != $bCount )
			{
				return false;
			}
			unset( $data['AUTO_PERSON_STATUS_STOP_COUNT'] );
		}
		unset( $data['EXTRA_PARAMS'] );

		if ( empty( C::_( 'WORKERS', $data ) ) )
		{
			return false;
		}

		$collect = [];
		$date = '';
		$Workers = explode( ',', C::_( 'WORKERS', $data ) );
		foreach ( $Workers as $worker )
		{
			$PD_date = PDate::Get( C::_( 'CHANGE_DATE', $data ) )->toFormat( '%Y-%m-%d' );
			if ( !$this->check_proccess_date( $worker, $PD_date ) )
			{
				XError::setError( 'upcoming proccess detected!' );
				return false;
			}

			$next_date = Job::next_change_date( $worker, $PD_date );
			if ( $next_date )
			{
				$data['CHANGE_DATE'] = $next_date;
			}

			$this->ChangesTable->resetAll();
			$checker = clone $this->ChangesTable;

			$Query = 'select x.* from slf_worker x where '
							. ' x.id = ' . DB::Quote( $worker )
							. ' and x.active = 1'
			;
			$dat = DB::LoadObject( $Query );
			$this->ChangesTable->bind( $dat );
			$this->ChangesTable->ID = '';
			$this->ChangesTable->WORKER_ID = $worker;
			$this->ChangesTable->CHANGE_TYPE = 3;
			$this->ChangesTable->RELEASE_TYPE = C::_( 'RELEASE_TYPE', $data );
			$this->ChangesTable->CHANGE_DATE = C::_( 'CHANGE_DATE', $data );
			$this->ChangesTable->RELEASE_FILES = implode( '|', C::_( 'RELEASE_FILES', $data ) );
			$this->ChangesTable->RELEASE_COMMENT = C::_( 'RELEASE_COMMENT', $data );
			$this->ChangesTable->STATUS = 0;
			$this->ChangesTable->CREATE_DATE = PDate::Get()->toFormat();
			$this->ChangesTable->CREATOR_PERSON = Users::GetUserID();
			$this->ChangesTable->AUTO_PERSON_STATUS_STOP = C::_( $worker, $AUTO_PERSON_STATUS_STOP, 0 );

			if ( !$this->ChangesTable->checkRelease() )
			{
				return false;
			}

			if ( !$this->ChangesTable->store() )
			{
				return false;
			}
			$collect[$worker] = $this->ChangesTable;
			if ( $date == '' )
			{
				$date = C::_( 'CHANGE_DATE', $data );
			}
		}
		Job::Release();
		return true;

	}

//	Helper  _______________________________________________
	public function Collector( $data, $worker, $type )
	{
		foreach ( $data as $key => $val )
		{
			if ( empty( $val ) )
			{
				unset( $data[$key] );
			}
			if ( !empty( C::_( 'ACCOUNTING_OFFICES', $data ) ) )
			{
				$ACCOUNTING_OFFICES = Helper::CleanArray( C::_( 'ACCOUNTING_OFFICES', $data, array() ), 'Int' );
				asort( $ACCOUNTING_OFFICES );
				$data['ACCOUNTING_OFFICES'] = implode( ',', $ACCOUNTING_OFFICES );
			}
		}
		$this->ChangesTable->resetAll();
		$this->ChangesTable->bind( $data );
		$this->ChangesTable->PERSON = $worker;
		$this->ChangesTable->CHANGE_TYPE = $type;
		$this->ChangesTable->STATUS = 0;
		$this->ChangesTable->CREATE_DATE = PDate::Get()->toFormat();
		$this->ChangesTable->CREATOR_PERSON = Users::GetUserID();
		return $this->ChangesTable;

	}

	public function CheckWorker( $data, $active = 1 )
	{
		$this->Table->resetAll();
		$this->Table->loads( array(
				'PERSON' => C::_( 'PERSON', $data ),
				'ORG' => C::_( 'ORG', $data ),
				'STAFF_SCHEDULE' => C::_( 'STAFF_SCHEDULE', $data ),
				'ACTIVE' => $active
		) );
		return C::_( 'ID', $this->Table );

	}

	public function CheckProcess( $data, $type = 0, $status = 0 )
	{
		$checker = clone $this->ChangesTable;
		$arr = [
				'PERSON' => C::_( 'PERSON', $data ),
				'ORG' => C::_( 'ORG', $data ),
				'STAFF_SCHEDULE' => C::_( 'STAFF_SCHEDULE', $data ),
				'STATUS' => $status
		];
		if ( $type > 0 )
		{
			$arr['CHANGE_TYPE'] = $type;
		}
		$checker->loads( $arr );
		return C::_( 'ID', $checker );

	}

	public function getAccountingOffices()
	{
		$ID = $this->Table->ID;
		if ( empty( $ID ) )
		{
			return '';
		}
		$query = 'select office from rel_accounting_offices where worker = ' . DB::Quote( $ID );
		return DB::LoadList( $query );

	}

	public function check_proccess_date( $id = '', $date = '' )
	{
		$query = ' select '
						. ' count(*) '
						. ' from slf_changes c where '
						. ' c.worker_id = ' . (int) $id
						. ' and trunc(c.change_date) > to_date(' . DB::Quote( $date ) . ',\'yyyy-mm-dd\') '
						. ' and c.status in (0, 1) '
						. ' and c.change_type not in (6) '
		;

		if ( (int) DB::LoadResult( $query ) > 0 )
		{
			return false;
		}

		return true;

	}

	public function check_same_schedule( $ids = '', $schedule_id = 0 )
	{
		$query = 'select w.staff_schedule from slf_worker w where w.id in (' . $ids . ') and w.active = 1 and w.staff_schedule = ' . (int) $schedule_id;
		if ( DB::LoadResult( $query ) > 0 )
		{
			return true;
		}

		return false;

	}

	public function get_schedule_salary( $id = 0 )
	{
		$query = 'select '
						. ' sc.salary '
						. ' from lib_staff_schedules sc '
						. ' where '
						. ' sc.id  = ' . (int) $id
		;
		return DB::LoadResult( $query );

	}

    public function existPendingScheduleChanging($ids)
    {
        $query = "
            SELECT 
            	* 
            FROM SLF_CHANGES sc 
            WHERE
                TRUNC(SYSDATE) < TRUNC(SC.CHANGE_DATE)
            AND
                sc.WORKER_ID IN (" . implode(',', $ids) . ") 
        ";

        return !empty(DB::LoadList($query));
    }

}
