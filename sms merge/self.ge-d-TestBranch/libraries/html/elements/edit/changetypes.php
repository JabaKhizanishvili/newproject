<?php

class JElementChangetypes extends JElement
{
	var $_name = 'changetypes';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Graphs = $this->getStandartGraphs();
		$options = array();
		$Start = new stdClass();
		$Start->ID = 0;
		$Start->LIB_TITLE = Text::_( 'select category' );
		$Start->LIB_DESC = '';
		$Start->FIELDS = '';

		$options[] = $Start;
		$ID = $control_name . $name . $name;
		$html = '<select name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" class="selectpicker form-control"  onchange="setChanges(\'' . $ID . '\');" >';
		$html .= $this->Options( $options, $value );
		$html .= $this->Options( $Graphs, $value );
		$html .= '</select>';
		return $html;
	}

	public function getStandartGraphs()
	{
		$Query = 'select t.id, t.lib_title, t.lib_desc, t.fields from LIB_CHANGE_TYPE t where t.active > -1 order by t.ordering asc';
		return DB::LoadObjectList( $Query );

	}

	/**
	 * Generates just the option tags for an HTML select list
	 *
	 * @param	array	An array of objects
	 * @param	string	The name of the object variable for the option value
	 * @param	string	The name of the object variable for the option text
	 * @param	mixed	The key that is selected (accepts an array or a string)
	 * @returns	string	HTML for the select list
	 */
	public static function options( $arr, $selected )
	{
		$html = '';
		foreach ( $arr as $option )
		{
			$Desc = C::_( 'LIB_DESC', $option );
			$Add = '';
			if ( !empty( $Desc ) )
			{
				$Add = ' ( ' . $Desc . ' )';
			}
			$k = $option->ID;
			$t = $option->LIB_TITLE . $Add;
			$extra = ( (string) $k == (string) $selected ? ' selected="selected"' : '' );
			$html .= '<option value="' . $k . '" ' . $extra . ' data-fields="' . str_replace( ' ', '', C::_( 'FIELDS', $option ) ) . '" >' . $t . '</option>';
		}
		return $html;

	}

}
