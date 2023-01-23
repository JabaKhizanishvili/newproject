<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class Person_operationModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new Person_operationTable();
		parent::__construct( $params );

	}

	public function getItem( $id = '', $key = 'ID', $type = 0 )
	{
		if ( empty( $id ) )
		{
			$id = C::_( 0, Request::getVar( 'nid', array() ), 0 );
		}

		if ( isset( $id ) && !empty( $id ) )
		{
			$load = [];
			if ( $key != 'ID' )
			{
				$load[$key] = $id;
			}
			else
			{
				$load['ID'] = $id;
			}

			if ( !empty( $type ) )
			{
				$load['CHANGE_TYPE'] = $type;
			}

			$this->Table->resetAll();
			$this->Table->loads( $load );
			$this->Table->SALARY = '';
//			$this->Table->CALCULUS_TYPE = '';
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		if ( empty( C::_( 'PERSON', $data ) ) )
		{
			return false;
		}

		$Slf_workerTable = new TableSlf_workerInterface( 'slf_worker', 'ID' );
		$Schedule = Xstaffschedule::getData( C::_( 'STAFF_SCHEDULE', $data ), 1 );

		$RelID = [];
		$ACCOUNTING_OFFICE = Helper::CleanArray( C::_( 'ACCOUNTING_OFFICES', $data, array() ), 'Int' );
		asort( $ACCOUNTING_OFFICE );

		$Workers = explode( ',', C::_( 'PERSON', $data ) );
		foreach ( $Workers as $Worker )
		{
			$this->Table->loads( array(
					'PERSON' => $Worker,
					'ORG' => C::_( 'ORG', $data ),
					'STAFF_SCHEDULE' => C::_( 'STAFF_SCHEDULE', $data ),
//					'ORG_PLACE' => C::_( 'ORG_PLACE', $Schedule ),
//					'POSITION' => C::_( 'POSITION', $Schedule ),
					'CHANGE_TYPE' => 2,
					'STATUS' => 0
			) );

			if ( !empty( C::_( 'ID', $this->Table ) ) && C::_( 'ID', $data ) != C::_( 'ID', $this->Table ) )
			{
				XError::setError( 'Process with this data already exists!' );
				return false;
			}

			$this->Table->resetAll();
			$this->Table->load( C::_( 'ID', $data ) );

			$Slf_workerTable->loads( array(
					'PERSON' => $Worker,
					'ORG' => C::_( 'ORG', $data ),
					'STAFF_SCHEDULE' => C::_( 'STAFF_SCHEDULE', $data ),
//					'ORG_PLACE' => C::_( 'ORG_PLACE', $Schedule ),
//					'POSITION' => C::_( 'POSITION', $Schedule ),
			) );

			if ( !empty( C::_( 'ID', $Slf_workerTable ) ) && C::_( 'WORKER_ID', $this->Table ) != C::_( 'ID', $Slf_workerTable ) )
			{
				XError::setError( 'Worker with this data already exists!' );
				return false;
			}

			$changedate = C::_( 'CHANGE_DATE', $this->Table );
			$changedate_time = PDate::Get( $changedate )->toFormat( '%H:%M:%S' );
			$data['CHANGE_DATE'] = PDate::Get( C::_( 'CHANGE_DATE', $data ) . ' ' . $changedate_time )->toFormat();

			if ( !$this->Table->bind( $data ) )
			{
				return false;
			}

			$this->Table->PERSON = $Worker;
			$this->Table->ACCOUNTING_OFFICES = implode( ',', $ACCOUNTING_OFFICE );
//			$this->Table->CHANGE_TYPE = 1;
			$this->Table->STATUS = 0;
			$this->Table->CREATE_DATE = PDate::Get()->toFormat();
			$this->Table->CREATOR_PERSON = Users::GetUserID();

//			staff_schedules
			$this->Table->bind( $Schedule );

			if ( C::_( 'SALARY', $data ) )
			{
				$this->Table->SALARY = C::_( 'SALARY', $data );
			}
			if ( C::_( 'CALCULUS_TYPE', $data ) )
			{
				$this->Table->CALCULUS_TYPE = C::_( 'CALCULUS_TYPE', $data );
			}

			if ( !$this->Table->check() )
			{
				return false;
			}

			$this_table = clone $this->Table;
			$type = C::_( 'CHANGE_TYPE', $this_table );
			if ( $type == 5 )
			{
				$token = C::_( 'TOKEN', $this_table );
				$table = $this->getItem( $token, 'TOKEN', 7 );
				$table->CHANGE_DATE = C::_( 'CHANGE_DATE', $this_table );
				$table->store();
			}

			if ( !$this_table->store() )
			{
				return false;
			}
			$RelID[] = $this_table->insertid();
		}

		if ( count( $RelID ) != count( $Workers ) )
		{
			return false;
		}
		return true;

	}

	public function StopProcess( $data )
	{
		$return = false;
		foreach ( $data as $id )
		{
			$this->Table->resetAll();
			$this->Table->load( $id );
			$type = C::_( 'CHANGE_TYPE', $this->Table );
			if ( $type == 7 || $type == 5 )
			{
				$token = C::_( 'TOKEN', $this->Table );
				$dat1 = $this->getItem( $token, 'TOKEN', 5 );
				$dat1->STATUS = -2;
				$dat1->store();

				$dat2 = $this->getItem( $token, 'TOKEN', 7 );
				$dat2->STATUS = -2;
				$dat2->store();

				return true;
			}

			if ( $this->Table->STATUS == 0 )
			{
				$this->Table->STATUS = -2;
				$this->Table->store();
				$return = true;
			}
		}
		return $return;

	}

}
