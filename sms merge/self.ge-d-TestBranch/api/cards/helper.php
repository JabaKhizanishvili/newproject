<?php

class APIReaderHelper
{
	public static function insertRecord( $Content )
	{
		$ID = C::_( '0', $Content );
		$CardID = C::_( '1', $Content );
		$Door = C::_( '2', $Content );
		$Name = C::_( '4', $Content );
		$Date = new PDate( C::_( '3', $Content ), 0 );
		$Query = 'insert into '
						. ' hrs_transported_data '
						. '(id, rec_date, access_point_code, card_id, cardname ) '
						. 'values '
						. ' ( '
						. ($ID ? DB::Quote( $ID ) : 'sqs_transported_data.nextval') . ','
						. 'to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\'),'
						. DB::Quote( $Door ) . ','
						. DB::Quote( $CardID ) . ','
						. DB::Quote( $Name )
						. ' ) ';
		try
		{
			DB::Insert( $Query, 'id' );
		}
		catch ( Exception $exc )
		{
			echo $exc->getTraceAsString();
		}

	}

	public static function CheckTransaction( $CardID, $Date )
	{
		$Query = ' select '
						. ' * '
						. ' from '
						. ' ('
						. ' select '
						. ' k.*, '
						. ' rownum as rn '
						. ' from '
						. ' ( '
						. ' select '
						. ' to_char( t.rec_date, \'yyyy-mm-dd hh24:mi:ss\') rec_date, '
						. ' t.door_type '
						. ' from hrs_transported_data t '
						. ' where '
						. ' t.card_id = ' . DB::Quote( $CardID )
						. ' and t.rec_date <=  to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\') '
						. '   and t.door_type < 100 '
						. ' order by t. rec_date desc'
						. ' ) k'
						. ' ) l '
						. '  where l.rn = 1 '

		;
		$Result = DB::LoadObject( $Query );
		if ( empty( $Result ) )
		{
			$Date = new PDate( 'now - 365 day' );
			$Result = array(
					'REC_DATE' => $Date->toFormat(),
					'DOOR_TYPE' => 0
			);
		}
		return $Result;

	}

}
