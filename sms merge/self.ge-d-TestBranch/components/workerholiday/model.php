<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class WorkerHolidayModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new HolidayTable( );
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
						. ' and w.org in (' . ($orgs ? $orgs : 0) . ') '
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
		if ( !$this->SaveData( $json_data, $data ) )
		{
			return false;
		}

		return true;

	}

	public function SaveData( $data, $replacers = [] )
	{
		if ( !empty( C::_( 'REPLACING_WORKERS', $data ) ) )
		{
			$Q = ' select '
							. ' w.org '
							. ' from hrs_workers w '
							. ' where '
							. ' w.id in (' . C::_( 'REPLACING_WORKERS', $data ) . ')'
			;
			$worgs = DB::LoadList( $Q );
			foreach ( $worgs as $v )
			{
				if ( !in_array( $v, C::_( 'ORG', $data ) ) )
				{
					return false;
				}
			}
		}

		$ID = C::_( 'ID', $data );
		if ( $ID )
		{
			$this->Table->load( $ID );
			$O = $this->Table->WORKER;
			$data['DAY_COUNT'] = Helper::getDayCount( $O, C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ) );
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

			$Flow = C::_( 'FLOW', $App );
			if ( $Flow )
			{
				TaskHelper::StartWorkFlow( $IDx );
			}
			else
			{
				Mail::sendLeaveEditMail( $O, C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ), Users::getUser(), $IDx );
				Mail::senLeaveEditSMS( $O, C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ), Users::getUser(), $IDx );
			}
		}
		else
		{
			$OrgIDx = $this->CheckData( $data );
			if ( empty( $OrgIDx ) )
			{
				return false;
			}

			foreach ( $OrgIDx as $OrgD )
			{
				$O = C::_( 'ID', $OrgD );
				$data['WORKER'] = $O;
				$data['ORG'] = C::_( 'ORG', $OrgD );
				$data['DAY_COUNT'] = Helper::getDayCount( $data['WORKER'], $data['START_DATE'], $data['END_DATE'] );
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

				$Flow = C::_( 'FLOW', $App );
				if ( $Flow )
				{
					TaskHelper::StartWorkFlow( $IDx );
				}
				else
				{
					Mail::sendNewLeaveMail( $O, C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ), Users::getUser(), $IDx );
					Mail::sendNewLeaveSMS( $O, C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ), Users::getUser(), $IDx );
				}
			}
		}
		return $IDx;

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$Date = new PDate();
				$this->Table->load( $id );
				if ( C::_( 'STATUS', $this->Table, 0 ) != 0 )
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

	public function CheckData( $data )
	{
		$Org = C::_( 'ORG', $data, array() );
		if ( empty( $Org ) && empty( $ID ) )
		{
			return false;
		}
		$OrgIDx = XGraph::getWorkerIDxByOrgs( $Org );
		if ( empty( $OrgIDx ) )
		{
			return false;
		}
		foreach ( $OrgIDx as $OrgD )
		{
			$data['REC_USER'] = Users::GetUserID();
			$data['WORKER'] = C::_( 'ID', $OrgD );
			$data['ORG'] = C::_( 'ORG', $OrgD );
			$data['DAY_COUNT'] = Helper::getDayCount( $data['WORKER'], $data['START_DATE'], $data['END_DATE'] );
			if ( !$data['DAY_COUNT'] )
			{
				XError::setError( 'leave day Count is zero!' );
				return false;
			}
			if ( !$this->Table->bind( $data ) )
			{
				return false;
			}
			if ( !$this->Table->check() )
			{
				return false;
			}
		}

		return $OrgIDx;

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
