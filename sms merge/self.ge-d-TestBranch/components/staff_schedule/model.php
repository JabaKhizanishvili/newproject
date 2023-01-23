<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class Staff_scheduleModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new Staff_schedulesTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
		if ( empty( $this->Table->ORG ) )
		{
			$this->Table->ORG = (int) trim( Request::getState( 'staff_schedules.display', 'org', '' ) );
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$ID = C::_( 'ID', $data, 0 );
		if ( $ID )
		{
			if ( C::_( 'ACTIVE', $data ) == 0 )
			{
				$exist = Xhelp::CheckWorkersInOrg( $ID, 'STAFF_SCHEDULE' );
				if ( count( $exist ) )
				{
					XError::setError( 'workers detected on staff_schedule!' );
					return false;
				}
			}
		}

		$Position = C::_( 'POSITION', $data );
		$NewPosition = C::_( 'POSITION_ADD', $data );
		if ( $Position == '-2' )
		{
			$NewPosition = C::_( 'LIB_TITLE', $data );
		}

		if ( !empty( $NewPosition ) )
		{
			$Pid = $this->registerNewPosition( $NewPosition );
			$data['POSITION'] = $Pid;
			unset( $data['POSITION_ADD'] );
		}

		$IDx = parent::SaveData( $data );
		$link = '?option=' . $this->_option;
//		XError::setMessage( 'Data Saved!' );		
//		Users::CloseConnection( $link );
		XStaffSchedule::Rebuild( C::_( 'ORG', $data ), $this->Table );
		return $IDx;

	}

	public function registerNewPosition( $title = '' )
	{
		if ( empty( $title ) )
		{
			return;
		}

		$lib_positions = new TableLib_positionsInterface( 'lib_positions', 'ID', 'library.nextval' );
		$lib_positions->loads( array(
				'LIB_TITLE' => trim( $title ),
				'ACTIVE' => 1
		) );

		if ( C::_( 'ID', $lib_positions ) > 0 )
		{
			return C::_( 'ID', $lib_positions );
		}

		$lib_positions->resetAll();
		$lib_positions->LIB_TITLE = trim( $title );
		$lib_positions->ACTIVE = 1;
		$lib_positions->ORDERING = 999;
		$lib_positions->store();
		$id = $lib_positions->insertid();
		return $id ? $id : 0;

	}

}
