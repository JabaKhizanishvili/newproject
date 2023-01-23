<?php

class Job
{
	public static function Assignment( $data = [], $date = '', $ScheduleChanging = false, $moveGraphData = false )
	{
		$Assignments = [];
		if ( count( $data ) )
		{
			if ( !empty( $date ) && !self::InDateHour( $date ) )
			{
				return false;
			}
			$Assignments = $data;
		}
		else
		{
			$Assignments = self::getChangesByType( '1' );
		}

		if ( count( $Assignments ) )
		{
			$Slf_workerTable = new TableSlf_workerInterface( 'slf_worker', 'ID', 'sqs_slf_worker.nextval' );
			$Slf_changesTable = new TableSlf_changesInterface( 'slf_changes', 'ID' );
			$Date = new PDate();
			$Slf_worker_relTable = new TableRel_person_orgInterface( 'rel_person_org', 'ID', 'sqs_rel_person_org.nextval' );

			$count = 0;
			foreach ( $Assignments as $one )
			{
				$Slf_worker_relTable->resetAll();
				$Slf_worker_relTable->loads( array(
						'ORG' => C::_( 'ORG', $one ),
						'PERSON' => C::_( 'PERSON', $one ),
						'ACTIVE' => 1
				) );

				$slf_worker_rel_ID = null;
				if ( !empty( C::_( 'ID', $Slf_worker_relTable ) ) )
				{
					$slf_worker_rel_ID = C::_( 'ID', $Slf_worker_relTable );
				}
				else
				{
					$Slf_worker_relTable->ORG = C::_( 'ORG', $one );
					$Slf_worker_relTable->PERSON = C::_( 'PERSON', $one );
					$Slf_worker_relTable->ACTIVE = 1;
					if ( empty( self::checkFirstSchedule( C::_( 'PERSON', $one ), C::_( 'ORG', $one ) ) ) )
					{
						$Slf_worker_relTable->ASSIGNMENT_DATE = C::_( 'CHANGE_DATE', $one );
					}

					if ( !$Slf_worker_relTable->store() )
					{
						continue;
					}
					$slf_worker_rel_ID = $Slf_worker_relTable->insertid();
				}

				$worker_exists = $Slf_workerTable->checkUnique( 'CHANGE_ID', C::_( 'ID', $one ) );
				$slf_worker_ID = null;

				$Slf_changesTable->resetAll();
				$Slf_changesTable->bind( $one );

				unset( $one->O_COMMENT );

				if ( !$worker_exists )
				{
					//Set slf_worker values
					$Slf_workerTable->resetAll();
					$Slf_workerTable->bind( $one );
					$Slf_workerTable->ID = '';
					$Slf_workerTable->ACTIVE = 1;
					$Slf_workerTable->CHANGE_ID = $one->ID;
					$Slf_workerTable->CHANGEDATE = C::_( 'CHANGE_DATE', $one );
					$Slf_workerTable->ORGPID = $slf_worker_rel_ID;
					if ( $ScheduleChanging )
					{
						$current_worker = XGraph::getWorkerDataSch( (int) C::_( 'WORKER_ID', $one ) );
						$current_schedule = C::_( 'STAFF_SCHEDULE', $current_worker );
						$Slf_workerTable->CHANGE_SCHEDULE = $current_schedule;
						$Slf_workerTable->CHANGE_SCHEDULE_WORKER = C::_( 'WORKER_ID', $one );
					}

					if ( !$Slf_workerTable->store() )
					{
						continue;
					}
					$slf_worker_ID = $Slf_workerTable->insertid();

                    if ($slf_worker_ID) {
                        $newWorkerId = $slf_worker_ID;
                        $oldWorkerId = C::_( 'WORKER_ID', $one );
                        if (!empty($oldWorkerId)) {
                            $updateHrsGraphQuery = "UPDATE hrs_graph hg set hg.worker = $newWorkerId where hg.worker = $oldWorkerId and TRUNC(hg.REAL_DATE) >= TRUNC(SYSDATE)";
                            DB::Query($updateHrsGraphQuery);
                        }
                    }

					//Relations _____________________________________
					self::SaveAccountingOfficesRel( C::_( 'ACCOUNTING_OFFICES', $one ), $slf_worker_ID );
					self::SaveChiefsRel( $slf_worker_ID, C::_( 'CHIEFS', $one ), C::_( 'ORG', $one ), 2 );
				}
				else
				{
					$Slf_workerTable->resetAll();
					$Slf_workerTable->load( C::_( 'ID', $one ), 'CHANGE_ID' );
					$slf_worker_ID = C::_( 'ID', $one );
				}

				$Query = 'UPDATE slf_changes x '
								. ' SET '
								. ' x.end_date = to_date(' . DB::Quote( $Date->toFormat() ) . ', \'YYYY-MM-DD HH24:MI:SS\') '
								. ' WHERE '
								. ' x.person = ' . DB::Quote( C::_( 'PERSON', $Slf_changesTable ) )
								. ' and x.org = ' . DB::Quote( C::_( 'ORG', $Slf_changesTable ) )
								. ' and x.status = 1 '
								. ' and x.end_date is null';
				DB::Update( $Query );

				$Slf_changesTable->START_DATE = $Date->toFormat();
				$Slf_changesTable->WORKER_ID = $slf_worker_ID;
				$Slf_changesTable->STATUS = 1;
				if ( !$Slf_changesTable->store() )
				{
					continue;
				}

				$count++;
			}

			if ( $count > 0 )
			{
				return true;
			}

			return false;
		}

	}

	public static function ScheduleChange( $data = [], $date = '' )
	{
		$Changes = $data;
		if ( !count( $data ) )
		{
			$Changes = self::getChangesByType( '5, 7' );
		}

		if ( !empty( $date ) && !self::InDateHour( $date ) )
		{
			return false;
		}

		if ( !count( $Changes ) )
		{
			return false;
		}

		foreach ( $Changes as $n => $data )
		{
			$type = C::_( 'CHANGE_TYPE', $data );
			if ( $type == 7 )
			{
				$Slf_workerTable = new TableSlf_workerInterface( 'slf_worker', 'ID', 'sqs_slf_worker.nextval' );
				$Slf_workerTable->load( $data->WORKER_ID );
				$Slf_workerTable->ACTIVE = -3;
				$Slf_workerTable->CHANGEDATE = C::_( 'CHANGE_DATE', $data );
				$Slf_workerTable->store();

				$Slf_changesTable = new TableSlf_changesInterface( 'slf_changes', 'ID' );
				$Slf_changesTable->load( C::_( 'ID', $data ) );
				$Slf_changesTable->STATUS = 1;
				$Slf_changesTable->store();
			}

			if ( $type == 5 )
			{
				if ( self::Assignment( [ $data ], $date, true, true ) )
				{
					return true;
				}
			}
		}

		return false;

	}

	public static function Change( $data = [], $date = '', $Ignore_hours = false )
	{
		$Changes = self::getChangesByType( '2, 4' );
		if ( count( $data ) )
		{
			if ( !$Ignore_hours && !empty( $date ) && !self::InDateHour( $date ) )
			{
				return false;
			}
			$Changes = $data;
		}

		if ( count( $Changes ) )
		{
			$Slf_workerTable = new TableSlf_workerInterface( 'slf_worker', 'ID', 'sqs_slf_worker.nextval' );
			$Slf_changesTable = new TableSlf_changesInterface( 'slf_changes', 'ID' );
			$Date = new PDate();

			$count = 0;
			foreach ( $Changes as $one )
			{
				$worker_exists = $Slf_workerTable->checkUnique( 'CHANGE_ID', C::_( 'ID', $one ) );
				$slf_worker_ID = null;
				$origin = clone $Slf_workerTable;
				$origin->load( C::_( 'WORKER_ID', $one ) );

				$Slf_changesTable->resetAll();
				$Slf_changesTable->bind( $one );

				unset( $one->O_COMMENT );
				if ( C::_( 'ORGPID', $origin ) && C::_( 'ORGPID', $one ) )
				{
					$origin->ORGPID = $one->ORGPID;
				}

				if ( !$worker_exists )
				{
					//Set slf_worker values
					$Slf_workerTable->resetAll();
					$Slf_workerTable->bind( $one );
					$Slf_workerTable->ID = C::_( 'ID', $origin );
					$Slf_workerTable->ACTIVE = C::_( 'ACTIVE', $origin );
					$Slf_workerTable->CHANGE_ID = $one->ID;
					$Slf_workerTable->CHANGEDATE = C::_( 'CHANGE_DATE', $one );
					if ( !$Slf_workerTable->store() )
					{
						continue;
					}
					//Save Accounting offices relation
					self::SaveAccountingOfficesRel( C::_( 'ACCOUNTING_OFFICES', $one ), C::_( 'WORKER_ID', $one ) );
					self::SaveChiefsRel( C::_( 'WORKER_ID', $one ), C::_( 'CHIEFS', $one ), C::_( 'ORG', $one ), 2 );
                    self::savePersonOrgAttributesRel(C::_( 'WORKER_ID', $one ), explode(',', C::_( 'ATTRIBUTES', $one )));
					$benefit_types = C::_( 'BENEFIT_TYPES', $one );
					$benefit_sub_type = C::_( 'CHANGE_SUB_TYPE', $one );
					if ( !empty( $benefit_types ) )
					{
						$binded_data = json_decode( $benefit_types );
						if ( $benefit_sub_type == 3 )
						{
							Benefits::delete_benefits( $binded_data );
						}
						else
						{
							Benefits::register_binding( $binded_data, $origin );
						}
					}

					if ( (int) C::_( 'GRAPHTYPE', $origin ) == 0 && (int) C::_( 'GRAPHTYPE', $one ) > 0 )
					{
						self::DelDinamicRel( C::_( 'WORKER_ID', $one ), C::_( 'ORG', $one ) );
					}
				}
				else
				{
					$Slf_workerTable->resetAll();
					$Slf_workerTable->load( C::_( 'ID', $one ), 'CHANGE_ID' );
					$slf_worker_ID = C::_( 'ID', $origin );
				}

				$Query = 'UPDATE slf_changes x '
								. ' SET '
								. ' x.end_date = to_date(' . DB::Quote( $Date->toFormat() ) . ', \'YYYY-MM-DD HH24:MI:SS\') '
								. ' WHERE '
								. ' x.person = ' . DB::Quote( C::_( 'PERSON', $Slf_changesTable ) )
								. ' and x.worker_id = ' . DB::Quote( C::_( 'WORKER_ID', $Slf_changesTable ) )
								. ' and x.org = ' . DB::Quote( C::_( 'ORG', $Slf_changesTable ) )
								. ' and x.status = 1 '
								. ' and x.end_date is null';
				DB::Update( $Query );

				$Slf_changesTable->START_DATE = $Date->toFormat();
				$Slf_changesTable->STATUS = 1;
				if ( !$Slf_changesTable->store() )
				{
					continue;
				}
				else
				{
					$diff = [];
					$past = [];
					foreach ( $Slf_workerTable as $key => $value )
					{
						if ( $origin->$key != $value )
						{
							$diff[$key] = $value;
							$past[$key] = $origin->$key;
						}
					}
				}

				$count++;
			}

			if ( $count > 0 )
			{
				return true;
			}

			return false;
		}

	}

	public static function Release( $data = [], $date = '' )
	{
		$Release = self::getChangesByType( '3' );
		if ( count( $data ) )
		{
			if ( !empty( $date ) && !self::InDateHour( $date ) )
			{
				return false;
			}
			$Release = $data;
		}

		if ( count( $Release ) )
		{
			$Slf_workerTable = new TableSlf_workerInterface( 'slf_worker', 'ID', 'sqs_slf_worker.nextval' );
			$Slf_changesTable = new TableSlf_changesInterface( 'slf_changes', 'ID' );
			$Slf_worker_relTable = new TableRel_person_orgInterface( 'rel_person_org', 'ID', 'sqs_rel_person_org.nextval' );
			$Date = new PDate();

			$count = 0;
			foreach ( $Release as $one )
			{
				$worker_exists = $Slf_workerTable->checkUnique( 'CHANGE_ID', C::_( 'ID', $one ) );
				$slf_worker_ID = null;
				$origin = clone $Slf_workerTable;
				$origin->load( C::_( 'WORKER_ID', $one ) );

				$Slf_changesTable->resetAll();
				$Slf_changesTable->bind( $one );

				unset( $one->O_COMMENT );

				if ( !$worker_exists )
				{
					//Set slf_worker values
					$Slf_workerTable->resetAll();
					$Slf_workerTable->bind( $one );
					$Slf_workerTable->ID = C::_( 'ID', $origin );
					$Slf_workerTable->ACTIVE = -2;
					$Slf_workerTable->CHANGE_ID = $one->ID;
					$Slf_workerTable->CHANGEDATE = C::_( 'CHANGE_DATE', $one );

					$wdata = XGraph::getWorkerDataSch( C::_( 'WORKER_ID', $one ) );
					$ch = self::checkFirstSchedule( C::_( 'PERSON', $one ), C::_( 'ORG', $one ) );
					if ( $ch == 1 )
					{
						$Slf_worker_relTable->load( C::_( 'ORGPID', $wdata ) );
						$Slf_worker_relTable->ACTIVE = -2;
						$Slf_worker_relTable->RELEASE_DATE = C::_( 'CHANGE_DATE', $one );
						$Slf_worker_relTable->store();
					}

					if ( !$Slf_workerTable->store() )
					{
						continue;
					}
					//Save Accounting offices relation
					//self::SaveAccountingOfficesRel( C::_( 'ACCOUNTING_OFFICES', $one ), C::_( 'WORKER_ID', $one ) );
				}
				else
				{
					$Slf_workerTable->resetAll();
					$Slf_workerTable->load( C::_( 'ID', $one ), 'CHANGE_ID' );
					$slf_worker_ID = C::_( 'ID', $origin );
				}

				$Query = 'UPDATE slf_changes x '
								. ' SET '
								. ' x.end_date = to_date(' . DB::Quote( $Date->toFormat() ) . ', \'YYYY-MM-DD HH24:MI:SS\') '
								. ' WHERE '
								. ' x.person = ' . DB::Quote( C::_( 'PERSON', $Slf_changesTable ) )
								. ' and x.worker_id = ' . DB::Quote( C::_( 'WORKER_ID', $Slf_changesTable ) )
								. ' and x.org = ' . DB::Quote( C::_( 'ORG', $Slf_changesTable ) )
								. ' and x.status = 1 '
								. ' and x.end_date is null';
				DB::Update( $Query );

				if ( C::_( 'AUTO_PERSON_STATUS_STOP', $one, 0 ) == 1 )
				{
					$qq = 'UPDATE SLF_PERSONS ss SET ss.ACTIVE = 0 WHERE ss.ID = ' . DB::Quote( C::_( 'PERSON', $one ) );
					DB::Update( $qq );
				}

				$Slf_changesTable->START_DATE = $Date->toFormat();
				$Slf_changesTable->STATUS = 1;
				if ( !$Slf_changesTable->store() )
				{
					continue;
				}
				else
				{
					$diff = [];
					$past = [];
					foreach ( $Slf_workerTable as $key => $value )
					{
						if ( $origin->$key != $value )
						{
							$diff[$key] = $value;
							$past[$key] = $origin->$key;
						}
					}
				}

				$count++;
			}

			if ( $count > 0 )
			{
				return true;
			}

			return false;
		}

	}

	//Checks _____________________________________________________________
	public static function InDateHour( $date )
	{
		$changeDate = new PDate( $date );

		$nowDate = PDate::Get();
		$nowHour = $nowDate->toFormat( '%H' );

		if ( $changeDate->toUnix() > $nowDate->toUnix() )
		{
			return false;
		}
		$jobHour = (int) Helper::getConfig( 'assignment_hour' );
		if ( $jobHour > $nowHour )
		{
			return false;
		}
		return true;

	}

	public static function getChangesByType( $typeId )
	{
		if ( !$typeId )
		{
			return [];
		}

		$order_by = ' order by tr.id asc, tr.change_date asc';

		$where = [];
		$where[] = ' trunc(tr.change_date) <= trunc(sysdate) ';
		$where[] = ' tr.change_type in (' . $typeId . ')';
		$where[] = ' tr.status = 0 ';

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$Q = 'select * from slf_changes tr '
						. $whereQ
						. $order_by
		;
		return DB::LoadObjectList( $Q );

	}

	public static function checkFirstSchedule( $person, $org )
	{
		$query = 'select count(*) from slf_worker w where '
						. ' w.person = ' . (int) $person
						. ' and w.org =  ' . (int) $org
						. ' and w.active > 0 '
		;
		return DB::LoadResult( $query );

	}

	public static function checkLastSchedule( $person )
	{
		$query = 'SELECT count(sw.ID) from slf_worker sw LEFT JOIN lib_unitorgs u ON u.id = sw.ORG where u.id IS NOT NULL AND u.ACTIVE = 1 AND  sw.person = (select	person	from slf_worker	 where	ID = ' . DB::Quote( $person ) . ') AND sw.ACTIVE = 1';
		return DB::LoadList( $query );

	}

	//Relations _____________________________________________________________
	public static function SaveAccountingOfficesRel( $Offices, $worker )
	{
		$DelQuery = 'delete '
						. ' from  rel_accounting_offices cp '
						. ' where '
						. ' cp.worker = ' . (int) $worker;

		$offices = explode( ',', $Offices );
		if ( !count( $offices ) )
		{
			return;
		}

		DB::Delete( $DelQuery );
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $offices as $office )
		{
			$query .= ' into rel_accounting_offices '
							. ' (worker, office) '
							. 'values '
							. '('
							. (int) $worker
							. ','
							. (int) $office
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

    public static function savePersonOrgAttributesRel($itemId, $attributeIds)
    {
        $AttributesTable = new TableRel_attributesInterface('rel_attributes', 'ID', 'sqs_rel_attributes.nextval');

        $query = "DELETE FROM rel_attributes WHERE ITEM_TYPE = 2 AND ITEM_ID = " . $itemId;

        DB::Query($query);

        if (!empty($attributeIds)) {
            foreach ($attributeIds as $attributeId) {

                $data = [
                    'ATTRIBUTE_ID' => $attributeId,
                    'ITEM_TYPE' => 2,
                    'ITEM_ID' => $itemId
                ];

                $AttributesTable->bind($data);
                $AttributesTable->store();

                $AttributesTable->resetAll();
            }
        }
    }

	public static function SaveChiefsRel( $worker, $Chiefs, $ORG, $type = 0 )
	{
		$DelQuery = 'delete '
						. ' from  rel_worker_chief_ext cp '
						. ' where '
						. ' cp.worker = ' . (int) $worker
//						. ' and cp.chief in (' . $Chiefs . ')'
//						. ' and cp.org= ' . (int) $ORG
//						. ' and cp.type= ' . (int) $type
		;
		DB::Delete( $DelQuery );

		if ( empty( $Chiefs ) )
		{
			return;
		}

		$chiefs = explode( ',', $Chiefs );
		if ( !count( $chiefs ) )
		{
			return;
		}

		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $chiefs as $chief )
		{
			if ( $chief > 0 )
			{
				$query .= ' into rel_worker_chief_ext '
								. ' (worker, chief, org, type) '
								. 'values '
								. '('
								. (int) $worker
								. ','
								. (int) $chief
								. ','
								. (int) $ORG
								. ','
								. (int) $type
								. ')';
			}
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

	public static function DelDinamicRel( $id, $ORGID )
	{
		$DelQuery = 'delete '
						. ' from  rel_workers_groups wp '
						. ' where '
						. ' wp.worker = ' . $id
						. ' and wp.org = ' . $ORGID
		;
		if ( !DB::Delete( $DelQuery ) )
		{
			return false;
		}
		return true;

	}

	public static function next_change_date( $id = '', $date = '' )
	{
		if ( empty( $id ) || empty( $date ) )
		{
			return false;
		}

		$query = 'select '
						. ' to_date(to_char(max(c.CHANGE_DATE), \'yyyy-mm-dd\'), \'yyyy-mm-dd\') + count(c.CHANGE_DATE) * 10 * (1/24/60) next_date '
						. ' from slf_changes c '
						. ' where '
						. ' c.worker_id = ' . (int) $id
						. ' and trunc(c.change_date) = to_date(' . DB::Quote( $date ) . ',\'yyyy-mm-dd\') '
						. ' and c.status >= 0 '
		;
		return (string) DB::LoadResult( $query );

	}

}
