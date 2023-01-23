<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class allsmsModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		parent::__construct( $params );

	}

	public function getItems()
	{
		$query = 'select t.* from system_allsms t ';
		$data = DB::LoadObjectList( $query, 'KEY' );
		$return = new stdClass();
		foreach ( $data as $d )
		{
			$key = $d->KEY;
			$return->{$key} = $d->VALUE;
		}
		return $return;

	}

	public function Prepare( $Data, $Print = 1 )
	{
		$org = C::_( 'ORG', $Data );
		$IE = C::_( 'IE_TYPE', $Data ) == 2 ? 'not' : '';

		$where = array();
		$where[] = 'w.id > 0';
		$where[] = ' w.active = 1 ';
		$where[] = ' w.mobile_phone_number is not null ';

		$Workers = implode( ',', Helper::CleanArray( explode( ',', C::_( 'WORKERS', $Data ) ) ) );
		if ( $Workers )
		{
			$where[] = ' w.id ' . $IE . ' in ( ' . $Workers . ' )';
		}
		$Units = implode( ',', Helper::CleanArray( explode( '|', C::_( 'UNIT', $Data ) ) ) );
		if ( $Units )
		{
			$where[] = ' w.org_place ' . $IE . ' in ( ' . $Units . ' ) ';
		}

		$sch = C::_( 'SCHEDULE', $Data );
		if ( !is_array( C::_( 'SCHEDULE', $Data ) ) )
		{
			$sch = explode( '|', C::_( 'SCHEDULE', $Data ) );
		}

		$Positions = implode( '\',\'', Helper::CleanArray( $sch, 'Str' ) );
		if ( $Positions )
		{
			$where[] = ' w.staff_schedule ' . $IE . ' in ( ' . '\'' . $Positions . '\'' . ' ) ';
		}

		$where[] = ' w.org = ' . DB::Quote( $org );
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		if ( $Print )
		{
			$Cols = ' sp.firstname || \' \'  || sp.lastname  Worker, '
							. ' sp.mobile_phone_number '
			;
		}
		else
		{
			$Cols = ' w.id, '
							. ' w.firstname, '
							. ' w.lastname, '
							. ' w.position, '
							. ' w.mobile_phone_number, '
							. ' u.lib_title as unit '
			;
		}
		$order_or_union = ' order by sp.lastname ';
//		if ( $Units && $IE == 'not' )
//		{
//			$order_or_union = ' UNION '
//							. ' select '
//							. $Cols
//							. ' from hrs_workers w '
//							. ' left join lib_units u on u.id = w.org_place where '
//							. ' (w.id > 0)'
//							. ' and ( w.active = 1 )'
//							. ' and ( w.mobile_phone_number is not null )'
//							. ' and ( w.org_place is null )'
//							. ' AND ( w.org = ' . DB::Quote( $org ) . ')'
//			;
//		}

		$Query = 'select '
						. $Cols
						. ' from  slf_persons sp '
						. ' where sp.id in ( '
						. ' select  w.parent_id '
						. ' from  hrs_workers_sch w '
						. ' left join lib_units u on u.id = w.org_place '
						. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
						. $whereQ
						. ' group by w.parent_id '
						. ' ) '
						. $order_or_union
		;

		$result = DB::LoadObjectList( $Query );
		if ( !count( $result ) )
		{
			$result = Text::_( 'Phone number not Detected!' );
		}
		$Data['SELECTED_WORKERS'] = json_encode( $result, JSON_UNESCAPED_UNICODE );
		return $Data;

	}

	public function CheckData( &$data )
	{
		$data['SMS'] = trim( C::_( 'SMS', $data, null ) );
		$data['UNIT'] = implode( '|', C::_( 'UNIT', $data, array() ) );
		$data['POSITIONS'] = implode( '|', Helper::CleanArray( C::_( 'POSITIONS', $data, array() ), 'Str' ) );
		if ( empty( $data['SMS'] ) )
		{
			return false;
		}
		else
		{
			return true;
		}

	}

	public function SendSMS( $data )
	{
		$Data = $this->Prepare( $data );
		$Workers = json_decode( C::_( 'SELECTED_WORKERS', $Data ) );
		$SMS = C::_( 'SMS', $Data );
		$SMSS = new oneWaySMS();
		foreach ( $Workers as $Worker )
		{
			$Mobile = C::_( 'MOBILE_PHONE_NUMBER', $Worker );
			$SMSS->Send( $Mobile, $SMSS->TranslitToLat( $SMS ) );
		}
		return true;

	}

}
