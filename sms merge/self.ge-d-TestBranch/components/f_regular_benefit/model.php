<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class f_regular_benefitModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new f_regular_benefitTable( );
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
		return parent::SaveData( $data );

	}

	public function DailyRecord()
	{
		if ( !Benefits::daily_records() )
		{
			return false;
		}

		return true;

	}

	public function Generate( $data )
	{
		$org = (int) trim( Request::getState( $this->_option_edit, 'org', false ) );
		$period = array_diff( Request::getState( $this->_option_edit, 'period', [] ), [ '' ] );

		if ( !$org || !count( $period ) )
		{
			return false;
		}

		if ( !Benefits::insert_worker_benefits( $org, $period ) )
		{
			return false;
		}

		return true;

	}

}
