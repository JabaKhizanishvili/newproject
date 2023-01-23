<?php

class APIReaderHelper
{
	public static function insertRecord( $Content )
	{
		$CardID = C::_( '0', $Content );
		$Door = C::_( '1', $Content );
		$Date = PDate::Get( C::_( '2', $Content ) );
		$User = Users::getUserByPermitID( $CardID );
		$UserID = (int) C::_( 'ID', $User, 0 );
		$UsersIDX = XGraph::getWorkerSCH_IDx( $UserID );
		$Diff = 4 * 60;
		$NowUnix = PDate::Get()->toUnix() - $Diff;
		if ( count( $UsersIDX ) )
		{
			foreach ( $UsersIDX as $UserOrg )
			{
				$ID = C::_( 'ID', $UserOrg );
				$Query = 'insert into '
								. ' hrs_transported_data '
								. '(id, rec_date, access_point_code, user_id, card_id, cardname, parent_id ) '
								. 'values '
								. ' ( '
								. 'sqs_transported_data.nextval,'
								. 'to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\'),'
								. DB::Quote( $Door ) . ','
								. DB::Quote( $ID ) . ','
								. DB::Quote( $CardID ) . ','
								. DB::Quote( '' ) . ','
								. DB::Quote( $UserID )
								. ' ) ';
				try
				{
					DB::Insert( $Query, 'ID' );
					XGraph::RecalculateOldEvents( $UserID, $Date->toFormat(), $Date->toFormat() );
				}
				catch ( Exception $exc )
				{
					echo $exc->getTraceAsString();
				}
			}
		}
		else
		{
			$ID = 0;
			$Query = 'insert into '
							. ' hrs_transported_data '
							. '(id, rec_date, access_point_code, user_id, card_id, cardname, parent_id ) '
							. 'values '
							. ' ( '
							. 'sqs_transported_data.nextval,'
							. 'to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\'),'
							. DB::Quote( $Door ) . ','
							. DB::Quote( $ID ) . ','
							. DB::Quote( $CardID ) . ','
							. DB::Quote( '' ) . ','
							. DB::Quote( $UserID )
							. ' ) ';
			try
			{
				DB::Insert( $Query, 'id' );
				XGraph::RecalculateOldEvents( $UserID, $Date->toFormat(), $Date->toFormat() );
			}
			catch ( Exception $exc )
			{
				echo $exc->getTraceAsString();
			}
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
