<?php

class Rollback
{
	protected static $Table = null;
	protected static $ChangesTable = null;

	public static function getCurrentItem( $id = 0 )
	{
		if ( empty( $id ) )
		{
			$id = Request::getVar( 'nid', array() )[0];
		}

		if ( isset( $id ) && !empty( $id ) )
		{
			$Q = 'select '
							. ' w.*,'
							. ' to_char(w.contracts_date, \'yyyy-mm-dd\') contracts_date, '
							. ' to_char(w.contract_end_date, \'yyyy-mm-dd\') contract_end_date '
							. ' from slf_worker w '
							. ' left join slf_changes c on c.id = w.change_id '
							. ' where '
							. ' c.change_type != 3 '
							. ' and w.id = ' . (int) $id
			;
			return DB::LoadObject( $Q );
		}

		return [];

	}

	public static function getPreviousItem( $id = 0, $equals = false, $not_id = false )
	{
		if ( empty( $id ) )
		{
			$id = Request::getVar( 'nid', array() )[0];
		}

		if ( !isset( $id ) || empty( $id ) )
		{
			return false;
		}

		$query = 'select '
						. ' c.*, '
						. ' to_char(c.contracts_date, \'yyyy-mm-dd\') contracts_date, '
						. ' to_char(c.contract_end_date, \'yyyy-mm-dd\') contract_end_date '
						. ' from slf_worker w '
						. ' left join slf_changes c on c.worker_id = w.id '
						. ' where '
						. ' w.id = ' . (int) $id
						. ' and c.id = (select max(cc.id) from slf_changes cc where cc.id ' . ($equals ? '=' : '!=') . ' w.change_id ' . ($not_id ? ' and cc.id != ' . (int) $not_id : '') . ' and cc.worker_id = w.id and cc.status = 1 and cc.change_type != 6) '
		;
		return DB::LoadObject( $query );

	}

	public static function save_rollback( $data )
	{
		self::$Table = new TableSlf_workerInterface( 'slf_worker', 'ID', 'sqs_slf_worker.nextval' );
		self::$ChangesTable = new TableSlf_changesInterface( 'slf_changes', 'ID', 'sqs_slf_change.nextval' );

		$worker = C::_( 'ID', $data, 0 );
		$current = self::getCurrentItem( $worker );
		$sch_id = C::_( 'CHANGE_SCHEDULE_WORKER', $current, false );
		$not_id = false;
		self::$Table->load( $worker );
		$active = C::_( 'ACTIVE', self::$Table );
		if ( empty( $current ) && $active == 1 )
		{
			$current = self::getPreviousItem( $worker );
			$not_id = C::_( 'ID', $current );
		}

		$date = PDate::Get();
		$Item_id = $sch_id ? $sch_id : $worker;
		$equals = $sch_id ? true : false;
		$prepare = self::getPreviousItem( $Item_id, $equals, $not_id );

		$change_id = C::_( 'CHANGE_ID', $current );
		if ( $not_id )
		{
			$change_id = $not_id;
		}

		$change_type = (int) C::_( 'CHANGE_TYPE', $prepare, 1 );
		if ( $change_type == 1 && !empty( $prepare ) )
		{
			$change_type = 2;
		}

		if ( $change_type == 1 )
		{
			$prepare = self::getPreviousItem( $Item_id, true, $not_id );
		}

		if ( empty( $current ) )
		{
			self::$Table->resetAll();
			self::$Table->load( $worker );
			$change_id = C::_( 'CHANGE_ID', self::$Table );
			$change_type = 3;
		}

		if ( !empty( $sch_id ) )
		{
			$change_type = 5;
		}

		if ( !self::set_rollbacked( $change_id ) )
		{
			return false;
		}

		self::$ChangesTable = self::collect_rollback( $worker, $change_type, $prepare, $date, $data );
		if ( !self::$ChangesTable->store() )
		{
			return false;
		}

		if ( $change_type == 2 )
		{
			Job::Change( [ $prepare ], $date, true );
		}

		return true;

	}

	public static function collect_rollback( $worker = 0, $change_type = 0, $data = [], $date = null, $params = null )
	{
		self::$ChangesTable->resetAll();

		switch ( $change_type )
		{
			case 1:
				if ( !self::assignment_rollback( $worker, $data ) )
				{
					return false;
				}
				break;
			case 2:
				if ( !self::change_rollback( $data ) )
				{
					return false;
				}
				break;
			case 3:
				if ( !self::release_rollback( $worker, $data ) )
				{
					return false;
				}
				break;
			case 5:
				if ( !self::schedule_rollback( $worker ) )
				{
					return false;
				}
				break;
			default :
				return false;
		}

		if ( !self::new_rollback( $worker, $date, $params ) )
		{
			return false;
		}

		return self::$ChangesTable;

	}

	public static function new_rollback( $worker = 0, $date = null, $params = null )
	{
		if ( empty( $worker ) )
		{
			return false;
		}

		if ( is_null( $date ) )
		{
			return false;
		}

		$day = $date->toFormat( '%Y-%m-%d' );
		self::$ChangesTable->ID = '';
		self::$ChangesTable->CHANGE_TYPE = 6;
		self::$ChangesTable->STATUS = 1;
		self::$ChangesTable->CHANGE_DATE = PDate::Get( $day )->toFormat();
		$next_date = Job::next_change_date( $worker, $day );
		if ( $next_date )
		{
			self::$ChangesTable->CHANGE_DATE = $next_date;
		}
		self::$ChangesTable->OPERATION_COMMENT = C::_( 'OPERATION_COMMENT', $params );
		self::$ChangesTable->CREATE_DATE = $date->toFormat();
		self::$ChangesTable->CREATOR_PERSON = Users::GetUserID();

		return true;

	}

	public static function schedule_rollback( $worker = 0 )
	{
		if ( empty( $worker ) )
		{
			return false;
		}

		self::$Table->resetAll();
		self::$Table->load( $worker );

		$ch = clone self::$ChangesTable;
		$changeid = C::_( 'CHANGE_ID', self::$Table );
		$ch->load( $changeid );
		self::$ChangesTable->loads( [
				'TOKEN' => C::_( 'TOKEN', $ch ),
				'CHANGE_TYPE' => 7
		] );
		self::$ChangesTable->STATUS = -6;
		self::$ChangesTable->store();

		self::$Table->ACTIVE = -6;
		$sch_id = C::_( 'CHANGE_SCHEDULE_WORKER', self::$Table, false );
		if ( !self::$Table->store() )
		{
			return false;
		}

		self::$Table->resetAll();
		self::$Table->load( $sch_id );
		self::$Table->ACTIVE = 1;
		if ( !self::$Table->store() )
		{
			return false;
		}

		return true;

	}

	public static function release_rollback( $worker = 0, $data = null )
	{
		if ( empty( $worker ) )
		{
			return false;
		}

		self::$Table->resetAll();
		self::$Table->load( $worker );
		self::$Table->ACTIVE = 1;

		$orgpid = self::$Table->ORGPID;
		$person = self::$Table->PERSON;

		if ( !self::$Table->store() )
		{
			return false;
		}

		$Slf_worker_relTable = new TableRel_person_orgInterface( 'rel_person_org', 'ID', 'sqs_rel_person_org.nextval' );
		$Slf_worker_relTable->load( $orgpid );
		$Slf_worker_relTable->ACTIVE = 1;
		$Slf_worker_relTable->RELEASE_DATE = '';
		if ( !$Slf_worker_relTable->store() )
		{
			return false;
		}

		$Slf_person_Table = new TableRel_person_orgInterface( 'slf_persons', 'ID', 'sqs_slf_person.nextval' );
		$Slf_person_Table->load( $person );
		$Slf_person_Table->ACTIVE = 1;
		if ( !$Slf_person_Table->store() )
		{
			return false;
		}

		self::$Table->resetAll();
		self::$Table->load( $worker );
		self::$Table->ACTIVE = 1;
		if ( !self::$Table->store() )
		{
			return false;
		}

		self::$ChangesTable->bind( $data );
		return true;

	}

	public static function change_rollback( $data = null )
	{
		if ( empty( $data ) )
		{
			return false;
		}

		self::$ChangesTable->bind( $data );
		return true;

	}

	public static function assignment_rollback( $worker = 0, $data = null )
	{
		if ( empty( $worker ) )
		{
			return false;
		}

		self::$Table->resetAll();
		self::$Table->load( $worker );
		self::$Table->ACTIVE = -6;
		if ( !self::$Table->store() )
		{
			return false;
		}

		self::$ChangesTable->bind( $data );
		return true;

	}

	public static function set_rollbacked( $change_id = 0 )
	{
		if ( empty( $change_id ) )
		{
			return false;
		}

		self::$ChangesTable->resetAll();
		self::$ChangesTable->load( $change_id );
		self::$ChangesTable->STATUS = -6;
		if ( !self::$ChangesTable->store() )
		{
			return false;
		}

		return true;

	}

}
