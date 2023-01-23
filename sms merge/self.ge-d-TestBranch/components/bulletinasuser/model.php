<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class bulletinasuserModel extends Model
{
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

	public function SaveData( $data )
	{	
		$Now = New PDate( );
		$Date = trim( C::_( 'START_DATE', $data ) );
		if ( empty( $Date ) )
		{
			return false;
		}

		$StartDate = New PDate( $Date );
		$this->Table->bind( $data );
		$this->Table->START_DATE = $StartDate->toFormat();
		$this->Table->ORG = XGraph::GetWorkerORGByID( $this->Table->WORKER );
		$this->Table->END_DATE = PDate::Get( $StartDate->toFormat() . ' +39 day' )->toFormat();
		$this->Table->APPROVE_DATE = $Now->toFormat();
		$this->Table->STATUS = 1;
		$this->Table->TYPE = APP_BULLETINS;
		$this->Table->DAY_COUNT = 0;
		$Approve = XGraph::GetApprove( $this->Table->ORG );
		$this->Table->APPROVE = Users::GetUserID();
		if ( $this->Exists( $Date, $this->Table->ORG, $this->Table->WORKER ) )
		{
			XError::setError( 'Bulletin Allready Registered!' );
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
		return $this->Table->insertid();

	}

	public function BContinue()
	{
		$idx = Request::getVar( 'nid', array() );
		if ( is_array( $idx ) )
		{
			$Workers = Helper::getChiefsWorkersIDx();
			foreach ( $idx as $id )
			{
				$this->Table->load( $id );
				if ( !C::_( 'ID', $this->Table ) )
				{
					continue;
				}
				if ( C::_( 'STATUS', $this->Table ) != 2 )
				{
					continue;
				}
				$ID = C::_( 'WORKER', $this->Table );
				if ( !isset( $Workers[$ID] ) )
				{
					continue;
				}
				$Date = new PDate( 'now + 30 day' );
				$this->Table->STATUS = 1;
				$this->Table->END_DATE = $Date->toFormat( '%Y-%m-%d 23:58:59' );
				$this->Table->store();
			}
			return true;
		}
		return false;

	}

	public function Exists( $Date, $Org, $O )
	{
		$Query = 'select '
						. ' a.id '
						. ' from hrs_applications a '
						. ' where '
						. ' to_date(\'' . PDate::Get( $Date )->toFormat() . '\', \'yyyy-mm-dd hh24:mi:ss\') between a.start_date and a.end_date '
						. ' and a.org= ' . $Org
						. ' and a.status= 1'
						. ' and a.type= ' . APP_BULLETINS
						. ' and a.worker= ' . $O
		;
		return DB::LoadResult( $Query );

	}

}
