<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class bulletinuserModel extends Model
{
	/**
	 * 
	 * @var 
	 */
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = AppHelper::getTable();
		parent::__construct( $params );

	}

	public function getItem()
	{
		return $this->Table;

	}

	public function getdata()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
		return $this->Table;

	}

	public function UploadBulletin( $data )
	{
		if ( empty( C::_( 'FILES', $data ) ) )
		{
			return false;
		}
		$FILES = implode( '|', C::_( 'FILES', $data ) );

		$this->Table->load( C::_( 'ID', $data ) );
		$this->Table->FILES = $FILES;
		if ( !$this->Table->check() )
		{
			return false;
		}
		if ( !$this->Table->store() )
		{
			return false;
		}
		return C::_( 'ID', $data );

	}

	public function SaveData( $data )
	{
		$ID = C::_( 'ID', $data );
		if ( !Xhelp::checkDate( C::_( 'START_DATE', $data ) ) )
		{
			return false;
		}
		if ( $ID )
		{
			$Date = trim( C::_( 'START_DATE', $data ) );
			if ( empty( $Date ) )
			{
				return false;
			}
			$StartDate = New PDate( $Date );
			$this->Table->load( $ID );
			$this->Table->bind( $data );
			$this->Table->START_DATE = $StartDate->toFormat();
			if ( !$this->Table->check() )
			{
				return false;
			}
			if ( !$this->Table->store() )
			{
				return false;
			}
			$IDx = $this->Table->insertid();
		}
		else
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

			$Date = trim( C::_( 'START_DATE', $data ) );
			if ( empty( $Date ) )
			{
				return false;
			}

			$StartDate = New PDate( $Date );
			foreach ( $OrgIDx as $OrgD )
			{
				$O = C::_( 'ID', $OrgD );
				$data['WORKER'] = $O;
				$data['REC_USER'] = Users::GetUserID();
				$data['ORG'] = C::_( 'ORG', $OrgD );
				if ( $this->Exists( $Date, C::_( 'UCOMMENT', $data, '' ), $O ) )
				{
					XError::setError( 'Bulletin Allready Registered!' );
					return false;
				}
				$Now = New PDate( );
				$StartDate = New PDate( $Date );
				$this->Table->load( $ID );
				$this->Table->bind( $data );
				$this->Table->WORKER = $O;
				$this->Table->START_DATE = $StartDate->toFormat( '%Y-%m-%d 00:00:00' );
				$this->Table->END_DATE = PDate::Get( $StartDate->toFormat() . ' +39 day' )->toFormat( '%Y-%m-%d 23:59:59' );
//				$this->Table->APPROVE_DATE = $Now->toFormat();
				$this->Table->STATUS = 1;
				$this->Table->TYPE = APP_BULLETINS;
				$this->Table->DAY_COUNT = 0;
//				$this->Table->APPROVE = Users::GetUserID();
				if ( !$this->Table->check() )
				{
					return false;
				}
				if ( !$this->Table->store() )
				{
					return false;
				}
				$IDx = $this->Table->insertid();
//				SEND MSG
				$WorkerData = XGraph::GetOrgUser( $O );
				$Subject = 'New bulletin request';
				$TextLines = [];
				$TextLines[] = 'ახალი ბიულეტენის განაცხადი';
				$TextLines[] = 'თანამშრომელი: ' . C::_( 'FIRSTNAME', $WorkerData ) . ' ' . C::_( 'LASTNAME', $WorkerData );
				$TextLines[] = 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $WorkerData );
				$TextLines[] = 'ბიულეტენის გახსნის თარიღი: ' . explode( ' ', C::_( 'START_DATE', $this->Table ) )[0];
				$TextLines[] = Uri::getInstance()->base() . '?option=bulletins';
				Mail::ToChiefs( $O, $Subject, $TextLines, 1, 1 );
			}
		}
		return $IDx;

	}

	public function Exists( $Date, $Org, $O )
	{
		$Query = 'select '
						. ' a.id '
						. ' from hrs_applications a '
						. ' where '
						. ' to_date(\'' . PDate::Get( $Date )->toFormat() . '\', \'yyyy-mm-dd hh24:mi:ss\') between a.start_date and a.end_date '
						. ' and a.org= ' . (int) $Org
						. ' and a.status= 1'
						. ' and a.type= ' . APP_BULLETINS
						. ' and a.worker= ' . (int) $O
		;
		return DB::LoadResult( $Query );

	}

}
