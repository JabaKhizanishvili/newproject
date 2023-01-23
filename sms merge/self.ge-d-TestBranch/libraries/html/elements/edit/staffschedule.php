<?php

class JElementStaffschedule extends JElement
{
	var $_name = 'staffschedule';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$exclude = $node->attributes( 'selfexclude' );
		$printdata = $node->attributes( 'printdata' );
		$ORG = $this->_parent->get( 'ORG' );
		if ( !$ORG )
		{
			$ORG = (int) trim( Request::getState( '.display', 'org', '' ) );
		}
		$MainUnits = Units::GetMainUnits( $ORG );
		$Graphs = $this->getSchedules( $value, $ORG, $exclude );
		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'SELECT CATEGORY' ) );
		foreach ( $Graphs as $Graph )
		{
			$MainUnit = C::_( $Graph->ORG_PLACE . '.TITLE', $MainUnits );
			$val = $Graph->ID;
			$text = XTranslate::_( $Graph->LIB_TITLE );
			$ULevel = $Graph->ULEVEL;
			if ( !empty( $Graph->LIB_DESC ) )
			{
				$text .= '  (' . $Graph->LIB_DESC . ') ';
			}
			if ( !empty( $MainUnit ) )
			{
				$text .= ' - ' . XTranslate::_( $MainUnit );
			}
			if ( !empty( $Graph->UNIT ) )
			{
				$text .= ' - ' . XTranslate::_( $Graph->UNIT );
			}
			$options[] = HTML::_( 'select.option', $val, str_repeat( '- ', $ULevel ) . $text );
		}
		Helper::SetJS( 'setGraphActior(\'' . $control_name . $name . '\');' );
		$return = HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="selectpicker form-control search-select ' . $control_name . '"  onchange="setGraphActior(\'' . $control_name . $name . '\');" ', 'value', 'text', $value, $control_name . $name );
		if ( $printdata )
		{
			$return .= '<div id="' . $control_name . $name . '_data" class="noheight schedule"></div>';
			$Link = '?option=s&service=staffschedule';
			Helper::SetJS(
							'var idi = $(".' . $control_name . '").val();'
							. 'if(idi)'
							. '{printData(idi);}'
							. '$(".' . $control_name . '").change(function(){'
							. 'var id = $(this).val();'
							. 'printData(id);'
							. '});'
							. 'function printData(id){'
							. ' if(id > 0){'
							. '$.ajax({
                                            url: "' . $Link . '",
                                            type: "POST",
                                            data: { "schedule": id},
                                            success: function(echo){'
							. ' $("#' . $control_name . $name . '_data").html(echo); '
							. ' $("#' . $control_name . $name . '_data").addClass("form-control"); '
							. '}});'
							. '}else{'
							. ' $("#' . $control_name . $name . '_data").removeClass("form-control").empty(); '
							. '}'
							. '}'
			);
		}
		return $return;

	}

	public function getSchedules( $value, $ORG, $exclude = '' )
	{
		$exclude_me = '';
		if ( $exclude && !empty( $value ) )
		{
			$exclude_me = ' and lu.id != ' . DB::Quote( $value );
		}
		$Query = 'select '
						. ' p.id, '
						. ' p.lib_title, '
						. ' p.lib_desc, '
						. ' lu.ulevel, '
						. ' p.org_place, '
						. ' lu.lib_title unit '
						. ' from LIB_STAFF_SCHEDULES p '
						. ' inner join lib_units lu on lu.id = p.org_place '
						. ' where '
						. ' p.active > 0 '
						. ' and p.org = ' . DB::Quote( $ORG )
						. $exclude_me
						. ' order by '
						. ' lu.lft asc, '
						. ' p.ordering asc'
		;
		return (array) XRedis::getDBCache( 'LIB_STAFF_SCHEDULES', $Query );
//		return DB::LoadObjectList( $Query );

	}

}
