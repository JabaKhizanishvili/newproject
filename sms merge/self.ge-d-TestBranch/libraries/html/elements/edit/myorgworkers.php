<?php

class JElementMyorgworkers extends JElement
{
	var $_name = 'myorgworkers';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Orgs = XGraph::GetMyOrgsIDx();
		$Link = '?option=s&service=ajaxworkers&org=' . implode( ',', $Orgs );
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
						. ' getWorkers(worker.data );'
						. '$("#' . $control_name . $name . '_autocomplete").val(""); '
						. '}'
						. '}'
						. ');'
						. '';
		$JS .= 'var WorkersData = "' . $value . '";'
						. 'if(WorkersData !="")'
						. '{'
						. 'getWorkers(WorkersData);'
						. '}';
		Helper::SetJS( $JS );
		return $return;

	}

}
