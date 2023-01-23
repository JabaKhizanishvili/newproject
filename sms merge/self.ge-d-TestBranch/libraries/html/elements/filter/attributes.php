<?php

// Created by Irakli Gzirishvili 27-10-2021.

class FilterElementAttributes extends FilterElement
{
	protected $_name = 'attributes';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$destinations = $node->attributes( 'destinations' );
		$Offices = $this->getAttributes( $destinations );
		$Value = explode( ',', $value );
		$options = [];
		foreach ( $Offices as $Item )
		{
			$options[] = HTML::_( 'select.option', $Item->ID, XTranslate::_( $Item->LIB_TITLE ) );
		}

		$html = '<input type="hidden" name="' . $name . '[]" />';
		$html .= '<select name="' . $name . '[]" id="' . $id . '" ' . ' multiple ' . ' class="form-control kbd search-select">';
		$html .= HTMLSelect::Options( $options, 'value', 'text', $Value, false );
		$html .= '</select>';

		return $html;

	}

	public function getAttributes( $destinations )
	{
		$Query = 'select la.id, la.lib_title
                    from lib_attributes la
                    where la.id > 0
                    and la.active = 1
                    and la.destination in (' . $destinations . ')
                    order by la.lib_title';
		$Data = (array) XRedis::getDBCache( 'lib_attributes', $Query, 'LoadObjectList' );
		return $Data;

	}

}
