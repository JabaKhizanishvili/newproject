<?php

class JElementPersonsall extends JElement
{
	var $_name = 'personsall';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		if ( is_array( $value ) )
		{
			$value = implode( ',', $value );
		}

		$assigned = $node->attributes( 'assigned' );
		$limitorg = $node->attributes( 'limitorg' );
		$ORG = C::_( '_registry._default.data.ORG', $this->_parent );
		if ( !$ORG )
		{
			$ORG = (int) trim( Request::getState( '.display', 'org', '' ) );
		}

		$JStype = '';
		$type = '';
		if ( $limitorg == 1 )
		{
			$type = '&type=assigned';
			$JStype = 'assigned';
		}

		$org = '';
		if ( $limitorg == 1 && $ORG )
		{
			$org = '&org=' . $ORG;
		}

		$Link = '?option=s&service=ajaxuniqworkers' . $type . $org;
		$return = '<div class="WorkersBlock">'
						. '<div class="WorkersContainer' . $name . '"></div>'
						. '<div class="cls"></div>'
						. '<div class="input-group">'
						. '<input type = "text" name = "" id = "' . $control_name . $name . '_autocomplete" class = "kbd form-control"/>'
						. '</div>'
						. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="" class="WorkersData' . $name . '" />'
						. '</div>';
		$JS = '$("#' . $control_name . $name . '_autocomplete").autocomplete('
						. '{'
						. 'minChars:3,'
						. 'autoSelectFirst:true,'
						. 'showNoSuggestionNotice:1,'
						. 'serviceUrl: "' . $Link . '",'
						. 'onSelect: function (worker) '
						. '{'
						. ' getUniqWorkers(worker.data , "' . $JStype . '","' . $name . '");'
						. '$("#' . $control_name . $name . '_autocomplete").val(""); '
						. '}'
						. '}'
						. ');'
						. '';
		$JS .= 'var WorkersData = "' . $value . '";'
						. 'if(WorkersData !="")'
						. '{'
						. 'getUniqWorkers(WorkersData, "' . $JStype . '","' . $name . '");'
						. '}';
		Helper::SetJS( $JS );
		return $return;

	}

}
