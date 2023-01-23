<?php

class JElementaccuracyperiods extends JElement
{
	var $_name = 'accuracyperiods';

	public function fetchElement( $name, $value, $node, $control_name )
	{

		$Graphs = $this->getAccuracyPeriod();
		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'SELECT CATEGORY' ) );
		foreach ( $node->children() as $option )
		{
			$val = $option->attributes( 'value' );
			$text = $option->data();
			$options[] = HTML::_( 'select.option', $val, Text::_( $text ) );
		}
		foreach ( $Graphs as $Graph )
		{
			$val = $Graph->ID;
			$text = XTranslate::_( $Graph->LIB_TITLE );
			if ( !empty( $Graph->LIB_DESC ) )
			{
				$text .= '  (' . $Graph->LIB_DESC . ') ';
			}
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="selectpicker form-control search-select ' . $control_name . '"  onchange="setGraphActior(\'' . $control_name . $name . '\');" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getAccuracyPeriod()
	{
		$Query = 'select p.id, p.lib_title, p.lib_desc from lib_F_accuracy_periods p where p.active > 0 order by p.lib_title asc';
		return DB::LoadObjectList( $Query );

	}

}
