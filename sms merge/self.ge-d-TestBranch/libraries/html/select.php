<?php

/**
 * Utility class for creating HTML select lists
 *
 * @static
 * @package 	WSCMS.Framework
 * @subpackage	HTML
 * @since		1.5
 */
class HTMLSelect
{
	/**
	 * @param	string	The value of the option
	 * @param	string	The text for the option
	 * @param	string	The returned object property name for the value
	 * @param	string	The returned object property name for the text
	 * @return	object
	 */
	public static function option( $value, $text = '', $value_name = 'value', $text_name = 'text', $disable = false, $Data = array() )
	{
		$obj = new stdClass;
		$obj->{$value_name} = $value;
		$obj->{$text_name} = trim( $text ) ? $text : $value;
		$obj->disable = $disable;
		return $obj;

	}

	/**
	 * @param	string	The text for the option
	 * @param	string	The returned object property name for the value
	 * @param	string	The returned object property name for the text
	 * @return	object
	 */
	public static function optgroup( $text, $value_name = 'value', $text_name = 'text' )
	{
		$obj = new stdClass;
		$obj->{$value_name} = '<OPTGROUP>';
		$obj->{$text_name} = $text;
		return $obj;

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
	public static function options( $arr, $key = 'value', $text = 'text', $selected = null, $translate = false )
	{
		$html = '';
		foreach ( $arr as $i => $option )
		{
			$element = & $arr[$i]; // since current doesn't return a reference, need to do this
			$isArray = is_array( $element );
			$extra = '';
			if ( $isArray )
			{
				$k = $element[$key];
				$t = $element[$text];
				$id = ( isset( $element['id'] ) ? $element['id'] : null );
				if ( isset( $element['disable'] ) && $element['disable'] )
				{
					$extra .= ' disabled="disabled"';
				}
			}
			else
			{
				$k = $element->{$key};
				$t = $element->{$text};
				$id = ( isset( $element->id ) ? $element->id : null );
				if ( isset( $element->disable ) && $element->disable )
				{
					$extra .= ' disabled="disabled"';
				}
			}

			// This is real dirty, open to suggestions,
			// barring doing a propper object to handle it
			if ( $k === '<OPTGROUP>' )
			{
				$html .= '<optgroup label="' . $t . '">';
			}
			else if ( $k === '</OPTGROUP>' )
			{
				$html .= '</optgroup>';
			}
			else
			{
				//if no string after hypen - take hypen out
				$splitText = explode( ' - ', $t, 2 );
				$t = $splitText[0];
				if ( isset( $splitText[1] ) )
				{
					$t .= ' - ' . $splitText[1];
				}

				//$extra = '';
				//$extra .= $id ? ' id="' . $arr[$i]->id . '"' : '';
				if ( is_array( $selected ) )
				{
					foreach ( $selected as $val )
					{
						$k2 = is_object( $val ) ? $val->{$key} : $val;
						if ( $k == $k2 )
						{
							$extra .= ' selected="selected"';
							break;
						}
					}
				}
				else
				{
					$extra .= ( (string) $k == (string) $selected ? ' selected="selected"' : '' );
				}

				//if flag translate text
				if ( $translate )
				{
					$t = Text::_( $t );
				}

				// ensure ampersands are encoded
				$k = FilterOutput::ampReplace( $k );
				$tt = FilterOutput::ampReplace( $t );

				$html .= '<option value="' . $k . '" ' . $extra . '>' . $tt . '</option>';
			}
		}

		return $html;

	}

	/**
	 * Generates an HTML select list
	 *
	 * @param	array	An array of objects
	 * @param	string	The value of the HTML name attribute
	 * @param	string	Additional HTML attributes for the <select> tag
	 * @param	string	The name of the object variable for the option value
	 * @param	string	The name of the object variable for the option text
	 * @param	mixed	The key that is selected (accepts an array or a string)
	 * @returns	string	HTML for the select list
	 */
	public static function genericlist( $arr, $name, $attribs = null, $key = 'value', $text = 'text', $selected = NULL, $idtag = false, $translate = false )
	{
		if ( is_array( $arr ) )
		{
			reset( $arr );
		}

		if ( is_array( $attribs ) )
		{
			$attribs = JArrayHelper::toString( $attribs );
		}
		if ( $idtag )
		{
			$id = $idtag;
		}
		else
		{
			$id = str_replace( ']', '', str_replace( '[', '', $name ) );
		}
		$html = '<select name="' . $name . '" id="' . $id . '" ' . $attribs . ' class="form-control">';
		$html .= HTMLSelect::Options( $arr, $key, $text, $selected, $translate );
		$html .= '</select>';

		return $html;

	}

	/**
	 * Generates a select list of integers
	 *
	 * @param int The start integer
	 * @param int The end integer
	 * @param int The increment
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @param string The printf format to be applied to the number
	 * @returns string HTML for the select list
	 */
	public static function integerlist( $start, $end, $inc, $name, $attribs = null, $selected = null, $format = "" )
	{
		$start = intval( $start );
		$end = intval( $end );
		$inc = intval( $inc );
		$arr = array();

		for ( $i = $start; $i <= $end; $i += $inc )
		{
			$fi = $format ? sprintf( "$format", $i ) : "$i";
			$arr[] = JHTML::_( 'select.option', $fi, $fi );
		}

		return JHTML::_( 'select.genericlist', $arr, $name, $attribs, 'value', 'text', $selected );

	}

	/**
	 * Generates an HTML radio list
	 *
	 * @param array An array of objects
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @param string The name of the object variable for the option value
	 * @param string The name of the object variable for the option text
	 * @returns string HTML for the select list
	 */
	public static function radiolist( $arr, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $node = null )
	{
		reset( $arr );
		$html = '';

		if ( is_array( $attribs ) )
		{
			$attribs = JArrayHelper::toString( $attribs );
		}

		$id_text = $name;
		if ( $idtag )
		{
			$id_text = $idtag;
		}

        $display = 'flex';

        if ($node && $node->attributes('inline')) {
            $display = 'inline-block';
        }

		$html .= '<div class="radiodiv" style="display: ' . $display . ';">';
		for ( $i = 0, $n = count( $arr ); $i < $n; $i++ )
		{
			$k = $arr[$i]->{$key};
			$t = $translate ? Text::_( $arr[$i]->{$text} ) : $arr[$i]->{$text};
			$id = ( isset( $arr[$i]->id ) ? $arr[$i]->id : null);

			$extra = '';
			$activeRadio = '';
			$activeRadioIcon = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if ( is_array( $selected ) )
			{
				foreach ( $selected as $val )
				{
					$k2 = is_object( $val ) ? $val->{$key} : $val;
					if ( $k == $k2 )
					{
						$extra .= " selected=\"selected\"";
						$activeRadio .= ' activeRadio ';
						$activeRadioIcon .= ' bi bi-check';
						break;
					}
				}
			}
			else
			{
				$extra .= ((string) $k == (string) $selected ? " checked=\"checked\"" : '');
				$extra .= ((string) $k == (string) $selected ? $activeRadio .= ' activeRadio ' : '');
				$extra .= ((string) $k == (string) $selected ? $activeRadioIcon .= ' bi bi-check' : '');
			}
			$html .= '<label class="radiochild ' . $activeRadio . '">';
			$html .= "\n\t<span class=\"checkmark\">";
			$html .= "<span class=\"" . $activeRadioIcon . "\"></span>";
			$html .= "</span>";
			$html .= "\n\t<input type=\"radio\" name=\"$name\" id=\"$id_text$k\" value=\"" . $k . "\"$extra $attribs />";
//			$html .= "\n\t<span for=\"$id_text$k\">$t</span>";
			$html .= "\n\t<span class=\"radiochild_lbl\">$t</span>";
			$html .= '</label>';
		}
		$html .= "</div>\n";
		return $html;

	}

	public static function checkbox( $arr, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false, $NL = false )
	{
		reset( $arr );
		$html = '';

		if ( is_array( $attribs ) )
		{
			$attribs = JArrayHelper::toString( $attribs );
		}

		$id_text = $name;
		if ( $idtag )
		{
			$id_text = $idtag;
		}

		for ( $i = 0, $n = count( $arr ); $i < $n; $i++ )
		{
			$k = $arr[$i]->{$key};
			$t = $translate ? Text::_( $arr[$i]->{$text} ) : $arr[$i]->{$text};
			$id = ( isset( $arr[$i]->id ) ? $arr[$i]->id : null);

			$extra = '';
			$extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if ( is_array( $selected ) )
			{
				foreach ( $selected as $val )
				{
					$k2 = is_object( $val ) ? $val->{$key} : $val;
					if ( $k == $k2 )
					{
						$extra .= " checked=\"checked\"";
						break;
					}
				}
			}
			else
			{
				$extra .= ((string) $k == (string) $selected ? " checked=\"checked\"" : '');
			}
			$html .= "\n\t<input type=\"checkbox\" name=\"$name\" id=\"$id_text$k\" value=\"" . $k . "\"$extra $attribs />";
			$html .= "\n\t<label for=\"$id_text$k\">$t</label>";
			if ( $NL )
			{
				$html .= '<br />';
			}
		}
		$html .= "\n";
		return $html;

	}

	/**
	 * Generates a yes/no radio list
	 *
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @returns string HTML for the radio list
	 */
	public static function booleanlist( $name, $attribs = null, $selected = null, $yes = 'yes', $no = 'no', $id = false )
	{
		$arr = array(
				JHTML::_( 'select.option', '0', Text::_( $no ) ),
				JHTML::_( 'select.option', '1', Text::_( $yes ) )
		);
		return JHTML::_( 'select.radiolist', $arr, $name, $attribs, 'value', 'text', (int) $selected, $id );

	}

}
