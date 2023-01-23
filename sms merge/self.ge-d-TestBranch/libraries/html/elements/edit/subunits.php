<?php

class JElementSubunits extends JElement
{
	var $_name = 'subunits';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Graphs = $this->getStandartGraphs();
		$options = array();
		$options[] = HTML::_( 'select.option', -1, ' - ' );
		foreach ( $Graphs as $Graph )
		{
			$val = $Graph->ID;
			$text = $Graph->LIB_TITLE;
			$ULevel = $Graph->ULEVEL;
			$options[] = HTML::_( 'select.option', $val, str_repeat( '- ', $ULevel ) . $text );
		}
		Helper::SetJS( 'setGraphActior(\'' . $control_name . $name . '\');' );
//		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="filter_droplist" ', 'value', 'text', $value, $control_name . $name );

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="selectpicker form-control"  onchange="setGraphActior(\'' . $control_name . $name . '\');" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getStandartGraphs()
	{
		$Query = 'select s.*
  from lib_units s
  inner join lib_units u
    on u.lft <= s.lft
   and u.rgt >= s.rgt
   and u.id = ' . $this->_parent->get( 'ORG_PLACE_DEF' )
						. ' where s.active = 1
   and s.org = ' . $this->_parent->get( 'ORG' )
						. ' order by s.lft';
		return DB::LoadObjectList( $Query );

	}

}
