<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

require_once PATH_BASE . DS . 'components' . DS . 'worker' . DS . 'table.php';

class o_smsModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new WorkersTable();
		parent::__construct( $params );

	}

	public function getItem( $ID )
	{
		$this->Table->load( (int) $ID );
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$Mobile = C::_( 'MOBILE_PHONE_NUMBER', $data );
		$SMSText = trim( C::_( 'SMS', $data ) );
		if ( empty( $Mobile ) )
		{
			return false;
		}
		if ( empty( $SMSText ) )
		{
			return false;
		}
		include_once BASE_PATH . DS . 'libraries' . DS . 'sms' . DS . 'oneway.php';
		$SMS = new oneWaySMS( );
		return $SMS->SendSMS( $SMSText, $Mobile );

	}

}
