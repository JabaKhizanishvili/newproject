<?php

class JElementGPersonsall extends JElement
{
	var $_name = 'Gpersonsall';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		if ( is_array( $value ) )
		{
			$value = implode( ',', $value );
		}
		$ID = $this->_parent->get( 'ID', 0 );
		$assigned = $node->attributes( 'assigned' );
		$limitorg = $node->attributes( 'limitorg' );
		$JStype = '';
		$type = '';
		if ( $limitorg == 1 )
		{
			$type = '&type=assigned';
			$JStype = 'assigned';
		}

		$org = '';

		$Link = '?option=s&service=ajaxuniqworkers' . $type . $org;
		$return = '<div class="WorkersBlock">'
						. '<div class="WorkersContainer"></div>'
						. '<div class="cls"></div>'
						. '<div class="input-group">'
						. '<input type = "text" name = "" id = "' . $control_name . $name . '_autocomplete" class = "kbd form-control"/>'
						. '</div>'
						. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="" class="WorkersData" />'
						. '</div>';
		$JS = '$("#' . $control_name . $name . '_autocomplete").autocomplete('
						. '{'
						. 'minChars:3,'
						. 'autoSelectFirst:true,'
						. 'showNoSuggestionNotice:1,'
						. 'serviceUrl: "' . $Link . '",'
						. 'onSelect: function (worker) '
						. '{'
						. ' getGWorkers(worker.data ,' . $ID . ', "' . $JStype . '");'
						. '$("#' . $control_name . $name . '_autocomplete").val(""); '
						. '}'
						. '}'
						. ');'
						. '';
		$JS .= 'var WorkersData = "' . $value . '";'
						. 'if(WorkersData !="")'
						. '{'
						. 'getGWorkers(WorkersData,' . $ID . ', "' . $JStype . '");'
						. '}';
		Helper::SetJS( $JS );
		return $return;

	}

}
