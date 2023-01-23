<?php

class JElementBatchWorkers extends JElement
{
	var $_name = 'BatchWorkers';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Workers = $this->getWorkers( (array) $value );
		$return = '<div class="WorkersBlock">'
						. '<div class="WorkersContainer">';

		foreach ( $Workers as $Worker )
		{
			$return .= '<div class="ContractItem">'
							. '<div class="ContractItem_name">' . C::_( 'WORKER_DATA', $Worker ) . '</div>'
							. '<input type="hidden" name="' . $control_name . '[' . $name . '][' . C::_( 'ID', $Worker ) . ']" id="' . $control_name . $name . '" value="' . C::_( 'ID', $Worker ) . '" />'
							. '<div class="cls"></div>'
							. '</div>';
		}
		$return .= '<div class="cls"></div>'
						. '</div>'
						. '</div>';
		return $return;

	}

	public function getWorkers( $WorkesIDx )
	{
		$WIDx = array();
		foreach ( $WorkesIDx as $ID )
		{
			$ID = (int) $ID;
			if ( empty( $ID ) )
			{
				continue;
			}
			$WIDx[] = $ID;
		}
		$Query = 'select '
						. ' k.id, '
						. ' u.firstname || \' \' || u.lastname || \' - \' ||  lss.lib_title as  worker_data'
						. ' from slf_worker k '
						. ' left join slf_persons u on u.id = k.person '
						. ' left join lib_staff_schedules lss on lss.id = k.staff_schedule '
						. ' where '
						. ' k.id in ('
						. '\'' . implode( '\',\'', $WIDx ) . '\''
						. ')'
		;
		return DB::LoadObjectList( $Query );

	}

}
