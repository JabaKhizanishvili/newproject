<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class HRGraphsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->group_id = (int) trim( Request::getState( $this->_space, 'group_id', '' ) );
		if ( empty( $Return->group_id ) )
		{
			$Groups = Helper::getAllWorkerGroups();
			$Return->group_id = C::_( '0.ID', $Groups );
		}


		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->workers = $this->getWorkers( $Return->group_id );
		if ( !Xhelp::checkDate( $Return->start_date ) )
		{
			$Return->start_date = '';
		}
		if ( empty( $Return->start_date ) )
		{
			$StartDate = new PDate();
			$Return->start_date = $StartDate->toFormat( '%d-%m-%Y' );
		}
		else
		{
			$StartDate = new PDate( $Return->start_date );
			$Return->start_date = $StartDate->toFormat( '%d-%m-%Y' );
		}
		if ( !Xhelp::checkDate( $Return->end_date ) )
		{
			$Return->end_date = '';
		}
		if ( empty( $Return->end_date ) )
		{
			$EndDate = new PDate( time() + 14 * 86400 );
			$Return->end_date = $EndDate->toFormat( '%d-%m-%Y' );
		}
		$Return->total = 0;
		$Return->items = array();
		return $Return;

	}

	public function getWorkers( $GroupID = 0 )
	{
		$Worker = trim( Request::getState( $this->_space, 'worker', 0 ) );
		if ( !$GroupID )
		{
			$GroupID = (int) trim( Request::getState( $this->_space, 'group_id', 0 ) );
		}
		if ( $Worker )
		{
			$GroupID = $this->getUserGroup( $Worker );
		}

		if ( $GroupID )
		{
			$Query = 'select '
							. ' w.*, '
							. ' lss.lib_title as sch_name '
							. ' from rel_workers_groups wg'
							. ' left join HRS_WORKERS_SCH w on w.id=wg.worker '
							. ' left join lib_staff_schedules lss on lss.id = w.staff_schedule '
							. ' where wg.group_id = ' . DB::Quote( (int) $GroupID )
							. ' and w.active = 1 '
							. ' and w.org = (select g.org from lib_workers_groups g where g.id = ' . DB::Quote( (int) $GroupID ) . ') '
							. ' order by wg.ordering asc '
			;
			return DB::LoadObjectList( $Query );
		}
		return array();

	}

	public function getUserGroup( $Worker )
	{
		static $WorkerGroup = null;
		if ( is_null( $WorkerGroup ) )
		{
			$Query = 'Select wg.group_id from rel_workers_groups wg where wg.worker =' . $Worker;
			$WorkerGroup = DB::LoadResult( $Query );
		}
		return$WorkerGroup;

	}

}
