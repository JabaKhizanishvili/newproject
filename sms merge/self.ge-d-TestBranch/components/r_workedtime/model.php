<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class r_workedtimeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new r_workedtimeTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
		if ( empty( $this->Table->ID ) )
		{
			return false;
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$this->Table->load( C::_( 'ID', $data ) );
		if ( empty( $this->Table->ID ) )
		{
			return false;
		}
		return parent::SaveData( $data );

	}

	public function Confirm( $data )
	{
		$this->Table = new TableHrs_worked_timesInterface( 'Hrs_worked_times', 'ID', 'sqs_hrs_worked_times.nextval' );
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$this->Table->load( $id );
				if ( empty( $this->Table->ID ) )
				{
					continue;
				}
				$this->Table->STATUS = 1;
				$this->Table->MODIFY_USER = Users::GetUserID();
				$this->Table->MODIFY_DATE = PDate::Get()->toFormat();
				$this->Table->store();
			}
		}
		return true;

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				if ( 'archive' == mb_strtolower( $mode ) )
				{
					$this->Table->load( $id );
					$this->Table->STATUS = -2;
					if ( property_exists( $this->Table, 'DEL_USER' ) && property_exists( $this->Table, 'DEL_DATE' ) )
					{
						$Date = new PDate();
						$this->Table->setDATE_FIELDS( 'DELETE_DATE', 'yyyy-mm-dd HH24:mi:ss' );
						$this->Table->DEL_USER = Users::GetUserID();
						$this->Table->DEL_DATE = $Date->toFormat();
					}
					$this->Table->store();
				}
				else
				{
					$this->Table->Delete( $id );
				}
			}
		}
		return true;

	}

}
