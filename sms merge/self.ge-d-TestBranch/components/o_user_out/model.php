<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

//require_once 'table.php';

class o_user_outModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = AppHelper::getTable();
		parent::__construct( $params );

	}

	public function SaveData( $data )
	{
		$WORKERS = C::_( 'WORKER', $data );
		if ( !is_array( $WORKERS ) )
		{
			$WORKERS = explode( ',', C::_( 'WORKER', $data ) );
		}
		if ( !Xhelp::checkDate( C::_( 'P_DATE', $data ) ) )
		{
			return false;
		}
		$TRANSPORTED_LOG = new TableHrs_transported_data_logInterface( 'hrs_transported_data_log', 'ID', 'sqs_logs.nextval' );
		$TRANSPORTED_LOG->setDATE_FIELDS( 'REC_DATE', 'yyyy-mm-dd hh24:mi:ss' );
		$TRANSPORTED_LOG->setDATE_FIELDS( 'LOG_DATE', 'yyyy-mm-dd hh24:mi:ss' );
		$LogIn = checkinuser::GetInstance();
		foreach ( $WORKERS as $worker )
		{
			$User = XGraph::getWorkerDataSch( $worker );
			$PDate = trim( C::_( 'P_DATE', $data ) );
			$PTime = trim( C::_( 'P_TIME', $data ) );
			if ( empty( $PDate ) )
			{
				return false;
			}
			if ( empty( $PTime ) )
			{
				return false;
			}
			$DoorCode = $this->getOutDoorCode();
			if ( empty( $DoorCode ) )
			{
				XError::setError( 'Doors Configuration error!' );
				return false;
			}
			$Date = new PDate( $PDate . ' ' . $PTime );
			$UserID = C::_( 'ID', $User );
			$PARENT_ID = C::_( 'PARENT_ID', $User );
			$TimeID = (int) XUserGraphs::RegisterEvent( $UserID, $Date->toFormat(), 1 );
			$Query = ' insert '
							. ' into HRS_TRANSPORTED_DATA '
							. ' ( '
							. ' ID, '
							. ' REC_DATE, '
							. ' ACCESS_POINT_CODE, '
							. ' USER_ID, '
							. ' CARD_ID, '
							. ' PARENT_ID, '
							. ' TIME_ID '
							. ' ) '
							. ' values '
							. ' ( '
							. DB::Quote( substr( md5( microtime() . 'sdDSADaAscVS DB HGF3WQSA##%#%$^dfc' ), 0, 16 ) ) . ', '
							. 'to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\'), '
							. DB::Quote( $DoorCode ) . ','
							. DB::Quote( $UserID ) . ','
							. DB::Quote( null ) . ','
							. DB::Quote( $PARENT_ID ) . ','
							. DB::Quote( $TimeID )
							. ' )'
			;
			if ( DB::Insert( $Query ) )
			{
				$LogIn->LogOut( C::_( 'PARENT_ID', $User ) );
				$TRANSPORTED_LOG->resetAll();
				$TRANSPORTED_LOG->REC_USER = Users::GetUserID();
				$TRANSPORTED_LOG->REC_DATE = $Date->toFormat();
				$TRANSPORTED_LOG->USER_ID = C::_( 'ID', $User );
				$TRANSPORTED_LOG->ACTION = 2;
				$TRANSPORTED_LOG->LOG_DATE = PDate::Get()->toFormat();
				$TRANSPORTED_LOG->store();
			}
		}
		XGraph::RecalculateOldEvents( $UserID, $Date->toFormat( '%Y-%m-%d' ), $Date->toFormat( '%Y-%m-%d' ) );
		return true;

	}

	public function getOutDoorCode()
	{
		return DB::LoadResult(
										' SELECT '
										. ' max(t.code) '
										. ' FROM lib_doors t '
										. ' WHERE '
										. ' t.type = 2 '
										. ' and t.active>-1 '
										. ' and t.defdoor = 1'
		);

	}

}
