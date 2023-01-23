<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

//require_once 'table.php';

class overtimeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = AppHelper::getTable();
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
		if ( $id )
		{
			$this->Table->load( $id );
		}

		$PDate = trim( C::_( 'START_DATE', $data ) );
		$DAY_COUNT = number_format( C::_( 'DAY_COUNT', $data ), 2 );
		if ( !is_numeric( $DAY_COUNT ) || $DAY_COUNT <= 0 )
		{
			XError::setError( 'Overtime Hour Incorrect!' );
			return false;
		}
		if ( empty( $PDate ) )
		{
			XError::setError( 'Date Incorrect!' );
			return false;
		}

		$O = XGraph::getWorkerDataSch( C::_( 'WORKER_ID', $data ) );
		$orgpid = C::_( 'ORGPID', $O );

		$BaseDate = new PDate( C::_( 'START_DATE', $data ) );
		$StartDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 00:00:00' ) );
		$EndDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 23:59:59' ) );
		$data['WORKER'] = $orgpid;
		$data['WORKER_ID'] = C::_( 'WORKER_ID', $data );
		if ( !$id )
		{
			$data['REC_USER'] = Users::GetUserID();
		}
		$data['TYPE'] = APP_OVERTIME;
		$data['START_DATE'] = $StartDate->toFormat();
		$data['END_DATE'] = $EndDate->toFormat();
		$data['DAY_COUNT'] = $DAY_COUNT;
		$data['INFO'] = trim( C::_( 'INFO', $data ) );
		$data['ORG'] = C::_( 'ORG', $data );
		if ( $EndDate->toUnix() > PDate::Get()->toUnix() )
		{
			XError::setError( 'overtime Date Incorrect!' );
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
		if ( !$this->Table->store() )
		{
			return false;
		}
		if ( !$id )
		{
//			SEND MSG
			$WorkerData = XGraph::GetOrgUser( $orgpid );
			$Subject = 'New overtime request';
			$TextLines = [];
			$TextLines[] = 'ახალი ზეგანაკვეთური დროის გასვლის განაცხადი';
			$TextLines[] = 'თანამშრომელი: ' . C::_( 'FIRSTNAME', $WorkerData ) . ' ' . C::_( 'LASTNAME', $WorkerData );
			$TextLines[] = 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $WorkerData );
			$TextLines[] = 'თარიღი: ' . explode( ' ', C::_( 'START_DATE', $this->Table ) )[0];
			$TextLines[] = 'საათი: ' . explode( ' ', C::_( 'DAY_COUNT', $this->Table ) )[0];
			$TextLines[] = Uri::getInstance()->base() . '?option=overtimeworkers';
			Mail::ToChiefs( $orgpid, $Subject, $TextLines, 1, 1 );
		}
		$IDx = $this->Table->insertid();
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
					XError::setError( 'Overtime Restriction!' );
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

}
