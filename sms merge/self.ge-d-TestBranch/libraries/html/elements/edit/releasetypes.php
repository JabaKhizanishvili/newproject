<?php

class JElementReleasetypes extends JElement
{
	var $_name = 'releasetypes';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Graphs = $this->getStandartGraphs();
		$options = array();
//		$options[] = HTML::_( 'select.option', -1, Text::_( 'Select Graph Type' ) );
		$options[] = HTML::_( 'select.option', 0, Text::_( 'SELECT CATEGORY' ) );
		foreach ( $Graphs as $Graph )
		{
			$val = $Graph->ID;
			$text = XTranslate::_( $Graph->LIB_TITLE );
			if ( !empty( $Graph->LIB_DESC ) )
			{
				$text .= '  (' . XTranslate::_( $Graph->LIB_DESC ) . ') ';
			}
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		Helper::SetJS( 'setGraphActior(\'' . $control_name . $name . '\');' );
//		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="filter_droplist" ', 'value', 'text', $value, $control_name . $name );

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="selectpicker form-control"  onchange="setGraphActior(\'' . $control_name . $name . '\');" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getStandartGraphs()
	{
		$Query = 'select p.id, p.lib_title, p.lib_desc from lib_release_type p where p.active > 0 order by p.lib_title asc';
		return DB::LoadObjectList( $Query );

	}

}
