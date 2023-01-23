<?php

// Created by Irakli Gzirishvili 28-10-2021.

class FilterElementActive extends FilterElement
{
	protected $_name = 'Active';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$options[] = HTML::_( 'select.option', -1, Text::_( 'CATEGORY FILTER' ) );
		$options[] = HTML::_( 'select.option', 1, Text::_( 'ON' ) );
		$options[] = HTML::_( 'select.option', 0, Text::_( 'OFF' ) );
		return HTML::_( 'select.genericlist', $options, $name, ' class="filter_droplist form-control" onchange="setFilter();" ', 'value', 'text', $value, $id );
	}
}


