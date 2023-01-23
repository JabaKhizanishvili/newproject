<?php

class FilterElementAccuracycodeperiods extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'accuracycodeperiods';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$Depts = $this->getAccuracyPeriod();

		$Key = $node->attributes( 'elementid' );
		if ( !empty( $Key ) )
		{
			$target = (int) C::_( 'data.' . $Key, $config, 0 );

			if ( $target > 0 )
			{
				$Depts = $this->getAccuracyPeriod( $target );
				$options[] = HTML::_( 'select.option', 0, Text::_( 'select category' ) );
			}
			else
			{
				$Depts = [];
				$options[] = HTML::_( 'select.option', 0, Text::_( 'select period' ) );
			}
		}
		else
		{
			$options[] = HTML::_( 'select.option', 0, Text::_( 'select category' ) );
		}

		foreach ( $Depts as $dept )
		{

			$val = $dept->ID;
			$start = $dept->P_START;
			$end = $dept->P_END;
			$text = explode( ' ', $start )[0] . ' - ' . explode( ' ', $end )[0];
//			$text = Xhelp::readPeriodTypeCode( $type, $val, $start1 );
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control search-select" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

	public function getAccuracyPeriod( $period_type = 0 )
	{
		$where = '';
		if ( $period_type > 0 )
		{
			$where = ' where p.pid = ' . (int) $period_type;
		}
		$Query = 'select p.* from slf_pay_periods p '
						. $where
						. ' and p.status = 1 '
						. ' order by p.p_code asc ';
		return DB::LoadObjectList( $Query );

	}

}
