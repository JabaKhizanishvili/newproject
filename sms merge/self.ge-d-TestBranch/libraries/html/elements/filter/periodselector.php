<?php

class FilterElementPeriodselector extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'periodselector';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$Depts = $this->getAccuracyPeriod();

		$html = '<input type="hidden" name="' . $name . '[]" />';
		$html .= '<select name="' . $name . '[]" id="' . $id . '" ' . ' multiple ' . ' class="form-control kbd search-select">';
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$type = $dept->PNAME;
			$start = $dept->P_START;
			$end = $dept->P_END;

			$text = '<i class="bi bi-1-circle-fill"></i>';
//			$text .= $val . ' - ';
			$text .= XTranslate::_( $type ) . ' / ';
			$text .= explode( ' ', $start )[0] . ' - ' . explode( ' ', $end )[0];
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		$html .= HTMLSelect::Options( $options, 'value', 'text', $value, false );
		$html .= '</select>';
		return $html;

	}

	public function getAccuracyPeriod()
	{
		$Query = 'select '
						. ' p.*, '
						. ' pp.lib_title pname '
						. ' from slf_pay_periods p '
						. ' left join lib_f_accuracy_periods pp on pp.id = p.pid '
						. ' where '
						. ' p.status != 2 '
						. ' order by pp.id asc ';
		return DB::LoadObjectList( $Query );

	}

}
