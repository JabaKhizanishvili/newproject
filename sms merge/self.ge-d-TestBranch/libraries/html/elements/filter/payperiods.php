<?php

class FilterElementPayperiods extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'payperiods';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$period_type = C::_( 'data.period_type', $config, 0 );
		$Depts = $this->getAccuracyPeriod( $period_type );

		if ( $period_type > 0 )
		{
			$options[] = HTML::_( 'select.option', 0, Text::_( 'select category' ) );
			foreach ( $Depts as $dept )
			{
				$val = $dept->ID;
				$start = $dept->P_START;
				$pname = $dept->P_NAME;
				$end = $dept->P_END;
				$text = $pname . ' / ' . explode( ' ', $start )[0] . ' - ' . explode( ' ', $end )[0];
				$options[] = HTML::_( 'select.option', $val, $text );
			}
		}
		else
		{
			$options[] = HTML::_( 'select.option', 0, Text::_( '- select benefit type -' ) );
		}

		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control search-select" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

	public function getAccuracyPeriod( $period_type = 0 )
	{
		$Query = 'select '
						. ' p.*, '
						. ' ac.lib_title p_name '
						. ' from slf_pay_periods p '
						. ' left join LIB_F_ACCURACY_PERIODS ac on ac.PERIOD_TYPE = p.P_TYPE '
						. ' where '
						. ' ac.period_type = p.p_type'
						. ' and p.p_start < sysdate '
						. ($period_type ? ' and p.pid = ' . (int) $period_type : '')
						. ' order by p.p_code desc ';
		return DB::LoadObjectList( $Query );

	}

}
