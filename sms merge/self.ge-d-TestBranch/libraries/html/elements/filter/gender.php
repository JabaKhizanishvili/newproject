<?php

// Created by Irakli Gzirishvili 27-10-2021.

class FilterElementGender extends FilterElement
{
	protected $_name = 'Gender';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$options[] = HTML::_( 'select.option', -1, Text::_( 'CATEGORY FILTER' ) );
		$options[] = HTML::_( 'select.option', 2, Text::_( 'FEMALE' ) );
		$options[] = HTML::_( 'select.option', 1, Text::_( 'MALE' ) );
		return HTML::_( 'select.genericlist', $options, $name, ' class="filter_droplist form-control" onchange="setFilter();" ', 'value', 'text', $value, $id );
	}
}

