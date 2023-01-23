<?php

class JElementCWorkers extends JElement
{
	var $_name = 'CWorkers';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$ORG = $this->_parent->get( 'ORG' );
		$Link = '?option=s&service=ajaxworkers';
		$return = '<div class="WorkersBlock">'
						. '<div class="WorkersContainer' . $ORG . '"></div>'
						. '<div class="cls"></div>'
						. '<div class="input-group">'
						. '<input type = "text" name = "" id = "' . $control_name . $name . '_autocomplete" class = "kbd form-control"/>'
						. '</div>'
						. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="" class="WorkersData' . $ORG . '" />'
						. '</div>';
		$JS = '$("#' . $control_name . $name . '_autocomplete").autocomplete('
						. '{'
						. 'minChars:3,'
						. 'autoSelectFirst:true,'
						. 'showNoSuggestionNotice:1,'
						. 'serviceUrl: "' . $Link . '",'
						. 'onSelect: function (worker) '
						. '{'
						. ' getWorkers(worker.data, ' . $ORG . ' );'
						. '$("#' . $control_name . $name . '_autocomplete").val(""); '
						. '}'
						. '}'
						. ');'
						. '';
		$JS .= 'var WorkersData' . $ORG . ' = "' . $value . '";'
						. 'if(WorkersData' . $ORG . ' !="")'
						. '{'
						. 'getWorkers(WorkersData' . $ORG . ', ' . $ORG . ');'
						. '}';
		Helper::SetJS( $JS );
		return $return;

	}

}
