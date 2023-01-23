<?php
/**
 * @version		$Id: text.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a text element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementBound extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'bound';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$boundto = $node->attributes( 'boundto' );
		$boundtotype = $node->attributes( 'boundtotype' );
		$when = $node->attributes( 'when' );
		$subtype = $node->attributes( 'subtype' );
		$value = '';
		if ( $valueIN )
		{
			$value = $valueIN;
		}
		$class = 'disabled class="text_area  form-control"';

		$js = ' function get_radio_values(element)
			{
				var radio_values = new Array();
				$(element).each(function(){
					$this = $(this);
					if($this.is(":checked") && $this.val() > -1)
					{
						radio_values.push($this.val());
					}
					else
					{
						if(radio_values.includes($this.val()))
						{
							var index = radio_values.indexOf($this.val());
							console.log(index);
							radio_values.splice(index, 1);
						}
					}
				});
				return radio_values;
			}
			function check_in_array(array1, array2)
			{
				var match = array1.filter(element => array2.includes(element));
				if(match.length > 0)
				{
					return true;
				}
				return false;
			}
			';

		$js .= ''
						. ' var id = "#params' . $name . '";'
						. ' var set = "#form-item-' . $name . '";'
						. ' var to = "' . ($boundtotype == 'radiolist' ? '#form-item-' . $boundto . ' .radio input' : '#params' . $boundto) . '";'
						. ' console.log(to);'
						. ' var when = [' . $when . ']; '
						. ' $(set).hide();'
						. ' boundParams(set, to, when); '
						. ' $(to).change(function(){'
						. ' boundParams(set, to, when); '
						. '}); '
						. ' function boundParams(set, to, when)'
						. '{'
						. ' var val = ' . ($boundtotype == 'radiolist' ? ' get_radio_values(to);' : ' $(to).val();')
						. '$(".bound").each(function(){ '
						. ' var rame = $(this).attr("when").split(",");'
						. ' var block = $(this).parent();'
						. ' var id = "#" + $(block).attr("id") + " .bound input, #" + $(block).attr("id") + " .bound select";'
						. ($boundtotype == 'radiolist' ? 'if(check_in_array(rame, val)) ' : 'if(rame.indexOf(val) > -1) ')
						. '{ '
						. ' $(block).show();'
						. ' $(id).each(function(){'
						. ' $(this).prop("disabled", false); '
						. ' }); '
						. ' } '
						. 'else'
						. '{'
						. ' $(block).hide();'
						. ' $(id).each(function(){'
						. ' $(this).prop("disabled", true); '
						. ' }); '
						. '}'
						. '});'
						. '}'
		;

		Helper::SetJS( $js );
		$html = '<div class="bound" when="' . $when . '">';
		if ( !empty( $subtype ) )
		{
			$html .= Xhelp::htmlElement( $subtype, 'edit', [ $name, $valueIN, $node, $control_name ] );
		}
		else
		{
			$html .= '<input type="text" name="' . $control_name . '[' . $name . ']"  id="' . $control_name . $name . '" value="' . $value . '" ' . $class . ' />';
		}
		$html .= '</div>';
		return $html;

	}

}
