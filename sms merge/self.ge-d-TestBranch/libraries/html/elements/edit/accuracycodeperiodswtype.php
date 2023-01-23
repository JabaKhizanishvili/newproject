<?php

class JElementAccuracycodeperiodswtype extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'accuracycodeperiodswtype';

	public function fetchElement( $name, $id, $node, $config )
	{
        $Graphs = $this->getStandartGraphs();
		$Depts = $this->getAccuracyPeriod();
        $options[] = HTML::_( 'select.option', 0, Text::_( 'select category' ) );

		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$start = $dept->P_START;
			$pname = $dept->P_NAME;
			$end = $dept->P_END;
			$text = $pname.' / '. explode( ' ', $start )[0] . ' - ' . explode( ' ', $end )[0];
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control search-select" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

	public function getAccuracyPeriod( $period_type = 0 )
	{
		$where = '';
		if ( $period_type > 0 )
		{
			$where = ' and p.pid = ' . (int) $period_type;
		}
		$Query = 'select p.*, ac.lib_title p_name from slf_pay_periods p '
                        . ' left join LIB_F_ACCURACY_PERIODS ac on ac.PERIOD_TYPE = p.P_TYPE '
						. ' where '
						. ' p.status = 1 '
                        . ' and ac.period_type = p.p_type'
						. $where
						. ' order by p.p_code asc ';
		return DB::LoadObjectList( $Query );

    }

}
