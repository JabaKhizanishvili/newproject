<?php

class XMissing
{
	public static function auto_insert( $day_back = 2, $percent = 0, $to_chief = 0 )
	{
		$Q = ' select '
						. ' k.event_date,	'
						. ' trunc(k.event_date) real_date, '
						. '	k.staff_id worker,'
						. ' k.lateness / k.working_time * 100 percent '
						. ' from ( '
						. ' SELECT '
						. ' e.event_date, '
						. ' e.staff_id, '
						. ' e.TIME_ID , '
						. ' gt.WORKING_TIME, '
						. ' GETLATENES(e.STAFF_ID, e.EVENT_DATE) lateness '
						. ' FROM hrs_staff_events e '
						. ' LEFT JOIN LIB_GRAPH_TIMES gt ON gt.ID = e.TIME_ID '
						. ' WHERE '
						. ' e.REAL_TYPE_ID = 2000 '
						. ' AND trunc(e.event_date) = trunc(sysdate) - ' . (int) $day_back
						. ' ) k '
						. ' LEFT JOIN hrs_applications ap ON ap.TYPE = ' . APP_MISSING
						. ' AND ap.WORKER = k.staff_id 	AND trunc(k.event_date) = trunc(ap.START_DATE) '
						. ' WHERE '
						. ' k.working_time > 0 '
						. ' AND k.lateness > 0 '
						. ' AND ap.ID IS NULL '
		;
		$graphs = DB::LoadObjectList( $Q, 'WORKER' );
		if ( !count( $graphs ) )
		{
			return false;
		}

		$Table = AppHelper::getTable();
		foreach ( $graphs as $worker => $data )
		{
			if ( $data->PERCENT < $percent )
			{
				continue;
			}

			$Table->resetAll();
			$worker_data = XGraph::getWorkerDataSch( $worker, 1 );
			$OrgPid = C::_( 'ORGPID', $worker_data );
			$BaseDate = PDate::Get( trim( C::_( 'REAL_DATE', $data ) ) );
			$StartDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 00:00:00' ) );
			$EndDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 23:59:59' ) );

			$Table->START_DATE = $StartDate->toFormat();
			$Table->END_DATE = $EndDate->toFormat();
			$Table->INFO = Text::_( 'Automatic' );
			$Table->WORKER = $OrgPid;
			$Table->WORKER_ID = $worker;
			$Table->TYPE = APP_MISSING;
			$Table->REC_DATE = PDate::Get()->toFormat();
			$Table->STATUS = 0;
			$Table->APPROVE = 0;
			$Table->DEL_USER = 0;
			$Table->SYNC = 0;

			$Table->store();
			if ( $to_chief == 1 )
			{
				$Subject = 'New missing record';
				$TextLines = [];
				$TextLines[] = 'გაცდენის ახალი განაცხადი';
				$TextLines[] = 'დაგენერირდა გაცდენის ახალი განაცხადი';
				$TextLines[] = 'თანამშრომელი: ' . C::_( 'FIRSTNAME', $worker_data ) . ' ' . C::_( 'LASTNAME', $worker_data );
				$TextLines[] = 'პ/ნ: ' . C::_( 'PRIVATE_NUMBER', $worker_data );
				$TextLines[] = 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $worker_data );
				$TextLines[] = 'შტატი: ' . C::_( 'SCHEDULE_NAME', $worker_data );
				$TextLines[] = 'გაცდენის თარიღი: ' . explode( ' ', C::_( 'START_DATE', $this->Table ) )[0];
				Mail::ToChiefs( $worker, $Subject, $TextLines, 1, 1 );
			}
		}

		return true;

	}

}
