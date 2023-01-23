<?php

class JElementgraphtype extends JElement
{
	var $_name = 'graphtype';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Graphs = $this->getStandartGraphs();
		$options = array();
//		$options[] = HTML::_( 'select.option', -1, Text::_( 'Select Graph Type' ) );
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Dinamic Graph' ) );
		foreach ( $Graphs as $Graph )
		{
			$val = $Graph->ID;
			$text = XTranslate::_( $Graph->LIB_TITLE );
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		Helper::SetJS( 'setGraphActior(\'' . $control_name . $name . '\');' );
//		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="filter_droplist" ', 'value', 'text', $value, $control_name . $name );

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="selectpicker form-control"  onchange="setGraphActior(\'' . $control_name . $name . '\');" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getStandartGraphs()
	{
		$Query = 'select t.id, t.lib_title from LIB_STANDARD_GRAPHS t where t.active = 1 order by t.lib_title asc';
		return DB::LoadObjectList( $Query );

	}

}
