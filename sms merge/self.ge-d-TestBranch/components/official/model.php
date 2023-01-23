<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

//require_once 'table.php';

class OfficialModel extends Model
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
		if ( $this->Table->ID )
		{
			$StartDate = new PDate( $this->Table->START_DATE );
			$EndDate = new PDate( $this->Table->END_DATE );
			$this->Table->START_TIME = $StartDate->toFormat( '%H:%M' );
			$this->Table->END_TIME = $EndDate->toFormat( '%H:%M' );
		}

		return $this->Table;

	}

	public function SaveData( $data )
	{
		if ( empty( C::_( 'WORKER', $data ) ) )
		{
			return false;
		}

		if ( empty( C::_( 'INFO', $data ) ) )
		{
			return false;
		}

		$WS = C::_( 'WORKER', $data );
		if ( !is_array( $WS ) )
		{
			$WS = explode( ',', $WS );
		}
		$collect = [];
		foreach ( $WS as $ID )
		{
			$WorkerData = XGraph::getWorkerDataSch( $ID );
			$UserDayTimes = AppHelper::GetUserDayDates( C::_( 'ID', $WorkerData ) );
			$DayEnd = C::_( 'DayEnd', $UserDayTimes );

			if ( !Xhelp::inNowDate( $DayEnd ) )
			{
				XError::setError( 'Worker not in working time!' );
				return false;
			}
			if ( AppHelper::HasApplication( $WorkerData, APP_OFFICIAL ) > 0 )
			{
				XError::setError( 'You already have registered application!' );
				return false;
			}
			$collect[$ID] = $WorkerData;
		}

		foreach ( $collect as $id => $Wdata )
		{
			$Reason = C::_( 'INFO', $data );
			$Comment = C::_( 'UCOMMENT', $data );
			AppHelper::RegisterOfficial( $Wdata, $Reason, $Comment, $ID );
		}
		return true;

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( !Helper::CheckTaskPermision( 'admin', 'officials' ) )
		{
			XError::setError( 'Holiday Already Approved!' );
			return false;
		}
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$Date = new PDate();
				$this->Table->load( $id );
				$this->Table->STATUS = -2;
				$this->Table->DEL_USER = Users::GetUserID();
				$this->Table->DEL_DATE = $Date->toFormat( '%Y-%m-%d %H:%M:%S' );
				$this->Table->store();
			}
		}
		return true;

	}

}
