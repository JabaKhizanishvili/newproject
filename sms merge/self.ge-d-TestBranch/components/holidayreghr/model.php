<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class HolidayRegHRModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new HolidayUserTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
		return $this->Table;

	}

	public function getDayList( $data = [] )
	{
		$Return = (object) [];
		$Return->data = $data;

		$start = PDate::Get( C::_( 'START_DATE', $data ) )->toFormat( '%Y-%m-%d' );
		$end = PDate::Get( C::_( 'END_DATE', $data ) )->toFormat( '%Y-%m-%d' );
		$orgs = implode( ', ', (array) C::_( 'ORG', $data ) );
		$replacers = C::_( 'REPLACING_WORKERS', $data );

		$query = 'select '
						. ' w.org, '
						. ' t.start_time, '
						. ' t.end_time, '
						. ' to_char(g.real_date, \'yyyy-mm-dd\') real_date, '
						. ' w.id ||\'|\'|| to_char(g.real_date, \'yyyy-mm-dd\') ||\'|\'|| g.time_id ||\'|\'|| g.id as id '
						. ' from hrs_graph g '
						. ' left join slf_worker w on w.id = g.worker '
						. ' left join slf_persons p on p.id = w.person '
						. ' left join lib_graph_times t on t.id = g.time_id '
						. ' where '
						. ' p.id = ' . Users::GetUserID()
						. ' and w.active = 1 '
						. ' and p.active = 1 '
						. ' and g.real_date >=  to_date(\'' . $start . '\', \'yyyy-mm-dd\') '
						. ' and g.real_date <=  to_date(\'' . $end . '\', \'yyyy-mm-dd\') '
//						. ' and w.org in (' . ($orgs ? $orgs : 0) . ') '
						. ' order by g.real_date desc '
		;

		$result = DB::LoadObjectList( $query );
		foreach ( $result as $d )
		{
			$d->REPLACING_WORKERS = $replacers;
		}

		$Return->items = $result;
		return $Return;

	}

	public function SaveDataRel( $data )
	{
		$json_data = (array) json_decode( Request::getVar( 'json' ) );
		if ( !$this->Save_Data( $json_data, $data ) )
		{
			return false;
		}

		return true;

	}

	public function Save_Data( $data, $replacers = [] )
	{
		$id = C::_( 'ID', $data );
		if ( $id )
		{
			$this->Table->load( $id );
		}
		else
		{
			$data['REC_USER'] = Users::GetUserID();
		}
		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}
		if ( !Xhelp::checkDate( $this->Table->START_DATE ) )
		{
			return false;
		}
		if ( !Xhelp::checkDate( $this->Table->END_DATE ) )
		{
			return false;
		}

		$data['DAY_COUNT'] = Helper::getDayCount( $data['WORKER'], $data['START_DATE'], $data['END_DATE'] );
		$data['ORG'] = XGraph::GetWorkerORGByID( $this->Table->WORKER );

		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}
		if ( !$this->Table->check() )
		{
			return false;
		}
		if ( !$this->Table->store() )
		{
			return false;
		}
		$App = TaskHelper::getLimitAppType( C::_( 'TYPE', $data ) );
		$IDx = $this->Table->insertid();
		$Flow = C::_( 'FLOW', $App );
		if ( $Flow )
		{
			TaskHelper::StartWorkFlow( $IDx );
		}

//		if ( $id )
//		{
//			Mail::sendLeaveEditMail( C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ), Users::getUser( C::_( 'WORKER', $data ) ) );
//		}
//		else
//		{
//			Mail::sendLeaveMail( C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ), Users::getUser( C::_( 'WORKER', $data ) ) );
//		}
		$IDx = $this->Table->insertid();

		$App = TaskHelper::getLimitAppType( C::_( 'TYPE', $data ) );
		$replacers_type = C::_( 'REPLACER_FIELD', $App );
		if ( in_array( $replacers_type, [ 3, 4 ] ) )
		{
			if ( empty( $replacers ) )
			{
				$datax = $this->getDayList( $data )->items;
				foreach ( $datax as $d )
				{
					$replacers[$d->ID] = $d->REPLACING_WORKERS;
				}
			}

			$this->SaveReplacerRel( $replacers, $IDx );
		}

		return $IDx;

	}

	public function Approve()
	{
		$idx = Request::getVar( 'nid', array() );
		$n = 0;
		if ( is_array( $idx ) )
		{
			$date = new PDate();

			foreach ( $idx as $id )
			{
				$this->Table->load( $id );
				if ( AppHelper::CheckDirectApprove( $this->Table->TYPE ) )
				{
					if ( C::_( 'ID', $this->Table ) )
					{
						$Worker = C::_( 'WORKER', $this->Table );
						$StartDate = C::_( 'START_DATE', $this->Table );
						$EndDate = C::_( 'END_DATE', $this->Table );
						$WorkerData = XGraph::GetOrgUser( $Worker );
						$SalaryID = C::_( 'ID', $WorkerData );
						$Type = (int) C::_( 'TYPE', $this->Table );

						$LimitsTable = new HolidayLimitsTable();
						$PDate = Pdate::Get( $StartDate );
						$LimitsTable->LoadUserLimits( $SalaryID, $PDate->toFormat(), $Type );
						if ( $LimitsTable->COUNT < $this->Table->DAY_COUNT )
						{
							XError::setError( 'HOLIDAY LIMIT  EXHAUSTED!' );
							continue;
						}
						$LimitsTable->COUNT = $LimitsTable->COUNT - $this->Table->DAY_COUNT;
						$LimitsTable->AWORKER = Users::GetUserID();

						if ( !$LimitsTable->store() )
						{
							continue;
						}
						$this->Table->STATUS = 1;
						$this->Table->APPROVE = Users::GetUserID();
						$this->Table->APPROVE_DATE = $date->toFormat( '%Y-%m-%d %H:%M:%S' );
						$this->Table->store();
						if ( $date->toUnix() > PDate::Get( $StartDate )->toUnix() )
						{
							$WIDx = XGraph::GetOrgUserIDByOrgID( $Worker );
							foreach ( $WIDx as $WID )
							{
								$Params = array(
										':p_date_start' => $StartDate,
										':p_date_end' => PDate::Get( PDate::Get( $EndDate )->toUnix() + 10 )->toFormat(),
										':p_worker' => $WID
								);
								DB::callProcedure( 'ReCalc', $Params );
							}
						}
						$TextLines = [];
						$TextLines[] = C::_( 'FIRSTNAME', $WorkerData ) . ' ' . C::_( 'LASTNAME', $WorkerData );
						$TextLines[] = 'თქვენი შვებულების განაცხადი  დადასტურებულია';
						$TextLines[] = 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $WorkerData );
						$TextLines[] = 'დაწყების თარიღი: ' . C::_( 'START_DATE', $this->Table );
						$TextLines[] = 'დასრულების თარიღი: ' . C::_( 'END_DATE', $this->Table );

						$Phone_Number = C::_( 'MOBILE_PHONE_NUMBER', $WorkerData );
						Mail::sendAppSMS( $Phone_Number, $TextLines );
						$n++;
					}
				}
				else
				{
					XError::setError( 'Holiday Have Flow!' );
					continue;
				}
			}
		}
		if ( $n == count( $idx ) )
		{
			return true;
		}

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$Date = new PDate();
				$this->Table->load( $id );
				if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) && C::_( 'STATUS', $this->Table, 0 ) != 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'Holiday Already Approved!' );
					Users::Redirect( $link );
				}
				$this->Table->STATUS = -2;
				$this->Table->DEL_USER = Users::GetUserID();
				$this->Table->DEL_DATE = $Date->toFormat( '%Y-%m-%d %H:%M:%S' );
				$this->Table->store();
			}
		}
		return true;

	}

	public function SaveReplacerRel( $replacers = [], $app = 0 )
	{
		if ( empty( $replacers ) || empty( $app ) )
		{
			return false;
		}

		$DelQuery = 'delete '
						. ' from rel_replace_workers wg '
						. ' where '
						. ' wg.app = ' . (int) $app;
		DB::Delete( $DelQuery );

		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $replacers as $key => $replacer )
		{
			$data = (array) explode( '|', $key );
			$worker = (int) C::_( 0, $data );
			$real_date = PDate::Get( C::_( 1, $data ) )->toFormat( '%Y-%m-%d' );
			$time_id = (int) C::_( 2, $data );
			$graph_id = (int) C::_( 3, $data );

			$query .= ' into ' . DB_SCHEMA . '.rel_replace_workers '
							. ' (worker, real_date, time_id, app, replacer, graph_id) '
							. 'values '
							. '('
							. $worker
							. ','
							. 'to_date(\'' . $real_date . '\', \'yyyy-mm-dd\')'
							. ','
							. $time_id
							. ','
							. $app
							. ','
							. (int) $replacer
							. ','
							. (int) $graph_id
							. ')'
			;
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

}
