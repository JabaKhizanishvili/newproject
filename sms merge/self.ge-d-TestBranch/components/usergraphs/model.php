<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class UserGraphsModel extends Model
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
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->workers = $this->getWorkers( $Return );
		if ( empty( $Return->group_id ) )
		{
			$WorkerGroups = XGraph::getWorkerGroups();
			$Return->group_id = C::_( '0.ID', $WorkerGroups, '' );
		}
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		if ( !Xhelp::checkDate( $Return->start_date ) )
		{
			$Return->start_date = '';
		}
		if ( empty( $Return->start_date ) )
		{
			$StartDate = new PDate();
			$Return->start_date = $StartDate->toFormat( '%d-%m-%Y' );
		}
		if ( !Xhelp::checkDate( $Return->end_date ) )
		{
			$Return->end_date = '';
		}
		if ( empty( $Return->end_date ) )
		{
			$EndDate = new PDate( time() + 7 * 86400 );
			$Return->end_date = $EndDate->toFormat( '%d-%m-%Y' );
		}
		$Return->total = 0;
		$Return->items = array();
		return $Return;

	}

	public function getWorkers( $Return )
	{
		$WorkerGroups = XGraph::getWorkerGroups();
		$group_id = (int) trim( Request::getState( $this->_space, 'group_id', C::_( '0.ID', $WorkerGroups, '' ) ) );
		if ( $group_id )
		{
			$Query = 'select w.* from HRS_WORKERS_SCH w '
							. ' left join rel_workers_groups wg on w.id=wg.worker '
							. ' where wg.group_id = ' . $group_id
							. '  and w.ID is not null '
							. ' order by wg.ordering asc '
			;
			return DB::LoadObjectList( $Query );
		}
		return array();

	}

}
