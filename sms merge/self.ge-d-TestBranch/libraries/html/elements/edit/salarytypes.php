<?php

class JElementSalarytypes extends JElement
{
	var $_name = 'salarytypes';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		if ( PAYROLL != 1 )
		{
			return false;
		}

		$Graphs = $this->salarytypes();
		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'SELECT CATEGORY' ) );
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
		Helper::SetJS( 'setGraphActior(\'' . $control_name . $name . '\');' );
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="selectpicker form-control search-select"  onchange="setGraphActior(\'' . $control_name . $name . '\');" ', 'value', 'text', $value, $control_name . $name );

	}

	public function salarytypes()
	{
		$Query = 'select  '
						. ' t.id, '
						. ' t.lib_title,  '
						. ' t.lib_desc  '
						. ' from lib_f_salary_types t  '
						. ' where '
						. ' t.active > -1'
						. ' order by ordering asc';
		return DB::LoadObjectList( $Query );

	}

}
