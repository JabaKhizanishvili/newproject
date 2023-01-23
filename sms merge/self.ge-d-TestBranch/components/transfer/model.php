<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class transferModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new hrs_transfers( );
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

	public function SaveData( $data )
	{
		$id = C::_( 'ID', $data );
		$ACCOUNTING_OFFICE = Helper::CleanArray( C::_( 'ACCOUNTING_OFFICE', $data, array() ), 'Int' );
		if ( $id )
		{
			$this->Table->load( $id );
		}
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
//		$User =  Users::getUser( C::_( 'WORKER', $data ) );
//		if ( $id )
//		{
//			Mail::sendLeaveEditMail( C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ), Users::getUser( C::_( 'WORKER', $data ) ) );
//		}
//		else
//		{
//			Mail::sendLeaveMail( C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ) );
//		}
		$this->SaveAccountingOfficesRel( $ACCOUNTING_OFFICE, $this->Table->ID );
		return $this->Table->insertid();

	}

	public function Approve()
	{
		$data = Request::getVar( 'params', array() );
		$ID = C::_( 'ID', $data );
		$this->Table->load( $ID );
		$this->Table->bind( $data );
		if ( !$this->Table->check_2() )
		{
			return false;
		}
		$this->Table->APPROVE = Users::GetUserID();
		$this->Table->STATUS = 1;
		$this->Table->APPROVE_DATE = PDate::Get()->toFormat();
		$this->Table->TRANSFER_DATE = PDate::Get( C::_( 'TRANSFER_DATE', $data ) )->toFormat( '%Y-%m-%d' );
		$this->Table->store();
		return $this->Table->insertid();

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

	public function CheckAccess( $var )
	{
		$idx = Request::getVar( 'nid', array() );
		if ( is_array( $idx ) )
		{
			if ( count( $idx ) > 1 )
			{
				XError::setError( 'You Must Approve Only One Record!' );
				return false;
			}

			$var = strtoupper( $var );
			$this->Table->load( C::_( '0', $idx ) );
			if ( C::_( 'STATUS', $this->Table ) == 2 )
			{
				XError::setError( 'Transfer Done!' );
				return false;
			}
			if ( C::_( 'STATUS', $this->Table ) == 1 )
			{
				XError::setError( 'Transfer Approved!' );
				return false;
			}
			$ids = array_flip( XGraph::getWorkerORGIDs() );
			if ( !isset( $ids[$this->Table->{$var}] ) )
			{
				XError::setError( 'You Are Tranfer Creator!' );
				return false;
			}
			return $this->Table;
		}
		return false;

	}

	public function SaveAccountingOfficesRel( $data, $id )
	{
		$DelQuery = 'delete '
						. ' from  rel_accounting_offices cp '
						. ' where '
						. ' cp.worker = ' . (int) $id;

		DB::Delete( $DelQuery );
		if ( !count( $data ) )
		{
			return;
		}
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $data as $DD )
		{
			$query .= ' into rel_accounting_offices '
							. ' (worker, office) '
							. 'values '
							. '('
							. (int) $id
							. ','
							. (int) $DD
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

}
