<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class GraphHistoryModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();

		$worker = (int) Request::getState( $this->_space, 'worker', '' );
		$day = (int) Request::getState( $this->_space, 'day', '' );
		$year = (int) Request::getState( $this->_space, 'year', '' );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		$where[] = ' a.worker = ' . $worker;
		$where[] = ' a.gt_day = ' . $day;
		$where[] = ' a.gt_year = ' . $year;

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_graph_hist a ' . $whereQ
		;

		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'SELECT'
						. ' a.HIST_START_DATE, '
						. ' a.HIST_END_DATE, '
						. ' a.id, '
						. ' a.worker, '
						. ' sp.FIRSTNAME, '
						. ' sp.LASTNAME, '
						. '	mp.FIRSTNAME m_FIRSTNAME, '
						. '	mp.LASTNAME m_LASTNAME, '
						. ' a.gt_day, '
						. ' a.gt_year, '
						. ' a.time_id, '
						. ' nvl(gt.LIB_TITLE, ' . DB::Quote( Text::_( 'Holiday' ) ) . ') graphtitle, '
						. ' a.real_date, '
						. ' a.change_woker, '
						. ' a.change_date '
						. ' FROM '
						. ' hrs_graph_hist a '
						. ' left join lib_graph_times gt on gt.id = a.time_id '
						. ' left join slf_worker sw on sw.id=a.worker '
						. '	left join slf_persons sp on sp.id = sw.person '
						. ' LEFT JOIN SLF_WORKER sm ON sm.id=a.modify_user '
						. ' LEFT JOIN SLF_PERSONS mp ON mp.ID = sm.PERSON '
						. $whereQ
						. $order_by
		;
		echo '<pre><pre>';
		print_r( $Query );
		echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";

		$Return->items = DB::LoadObjectList( $Query );
		return $Return;

	}

	public function getWorkerNameAndDay( $worker, $day, $year )
	{

		$dat = $this->DayOfYear2Date( $year, $day );
		$date = new PDate( $dat );

		$weekday = $date->toFormat( '%A' );
		$dayAndMonth = $date->toFormat( '%d %B' );

//		$User = XGraph::GetOrgUser( $worker );

		$dbResult = []; // DB::LoadObject( $Query );

		$retData = new stdClass;
		$retData->weekday = $weekday;
		$retData->dayAndMonth = $dayAndMonth;
		$retData->workerName = C::_( 'WORKERNAME', $dbResult );

		return $retData;

	}

	protected function DayOfYear2Date( $year, $DayInYear )
	{
		$d = new DateTime( $year . '-01-01' );
		date_modify( $d, '+' . ($DayInYear - 1) . ' days' );
		return $d->format( 'Y-m-d' );

	}

}
