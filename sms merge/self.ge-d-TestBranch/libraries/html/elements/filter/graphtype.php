<?php

// Created by Irakli Gzirishvili 27-10-2021.

class FilterElementGraphtype extends FilterElement
{
	protected $_name = 'Graphtype';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$query = 'select * from LIB_STANDARD_GRAPHS g '
						. ' where g.active = 1 '
						. ' order by g.lib_title asc'
		;
		$GraphTypes = DB::loadObjectList( $query, $value );
		$options[] = HTML::_( 'select.option', -1, Text::_( 'CATEGORY FILTER' ) );
		$options[] = HTML::_( 'select.option', 0, Text::_( 'DINAMIC GRAPH' ) );
		foreach ( $GraphTypes as $GraphType )
		{
			$val = $GraphType->ID;
			$text = XTranslate::_( $GraphType->LIB_TITLE );
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, $name, ' class="filter_droplist form-control" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

}
