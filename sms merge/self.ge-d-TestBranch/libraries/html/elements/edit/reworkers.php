<?php

class JElementReWorkers extends JElement
{
	var $_name = 'ReWorkers';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Key = $node->attributes( 'key', 'ID' );
		$KeyID = $this->_parent->get( $Key );
		$Link = '?option=s&service=ajaxchiefworkers';
		$return = '<div class="WorkersBlock">
        <div class="WorkersContainer"></div>
        <div class="cls"></div>
        <div class="WorkersButtons">'
						. '<input type="text" name="" id="' . $control_name . $name . '_autocomplete" class="kbd form-control"/>'
						. '</div>'
						. ' <input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="" class="WorkersData" />'
						. '</div>';
//		$return = ''
//						. '<div class="WorkersBlock">'
//						. '<div class="WorkerContainerNew" id="WorkerContainerNew"></div>'
//						. '<div class="cls"></div>'
//						. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="" class="WorkersData" />'
//						. '</div>'
//						. '<script id="ajaxworkerTMPL" type="text/x-jquery-tmpl">'
//						. '<div class="WorkertItem">'
//						. '<div class="WorkerItem_name">${WORKER}</div>'
//						. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="${ID}" />'
//						. '<div class="cls"></div>'
//						. '</div>'
//						. '</script>';
		$JS = '$("#' . $control_name . $name . '_autocomplete").autocomplete('
						. '{'
						. 'minChars:3,'
						. 'showNoSuggestionNotice:1,'
						. 'serviceUrl: "' . $Link . '",'
						. 'onSelect: function (worker) '
						. '{'
						. ' getReWorkers(worker.data, ' . $KeyID . ' ) ;'
						. '$("#' . $control_name . $name . '_autocomplete").val(""); '
						. '}'
						. '}'
						. ');'
						. '';
		$JS .= 'var WorkersData = "' . $value . '";'
						. 'if(WorkersData!="")'
						. '{'
						. 'getReWorkers(WorkersData, ' . $KeyID . ' );'
						. '}';
		Helper::SetJS( $JS );
		return $return;

	}

}
