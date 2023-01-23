<?php

class JElementSWorker extends JElement
{
	var $_name = 'SWorker';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$org = $node->attributes( 'limitorg' );
		$ORG = '';
		if ( $org == 1 )
		{
			if ( $get = Request::getVar( 'params', array() ) )
			{
				$ORG = '&org=' . $get['ORG'];
			}
			elseif ( $get = C::_( '_registry._default.data.ORG', $this->_parent ) )
			{
				$ORG = '&org=' . $get;
			}
		}

		$Link = '?option=s&service=ajaxworkers' . $ORG;
		$return = '<div class="WorkersBlock">';
		if ( $value )
		{
			$Worker = XGraph::GetOrgUser( $value );
			$return .= '<div class="WorkerContainerNew" id="WorkerContainerNew">'
							. '<div class="WorkertItem">'
							. '<div class="WorkerItem_name">' . C::_( 'FIRSTNAME', $Worker ) . ' ' . C::_( 'LASTNAME', $Worker ) . ' - ' . C::_( 'POSITION', $Worker ) . ' - ' . C::_( 'ORG_NAME', $Worker ) . '</div>'
							. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . C::_( 'ID', $Worker ) . '" />'
							. '<div class="cls"></div>'
							. '</div>'
							. '</div>';
		}
		else
		{
			$return .= '<div class="WorkerContainerNew" id="WorkerContainerNew"></div>';
		}
		$return .= '<div class="cls"></div>'
						. '<div class="input-group">'
						. '<input type="text" name="" id="' . $control_name . $name . '_autocomplete" class="kbd form-control" />'
						. '</div>'
						. '</div>'
						. '<script id="ajaxworkerTMPL" type="text/x-jquery-tmpl">'
						. '<div class="WorkertItem">'
						. '<div class="WorkerItem_name">${WORKER}</div>'
						. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="${ID}" />'
						. '<div class="cls"></div>'
						. '</div>'
						. '</script>';
		$JS = '$("#' . $control_name . $name . '_autocomplete").autocomplete('
						. '{'
						. 'minChars:3,'
						. 'autoSelectFirst:true,'
						. 'showNoSuggestionNotice:1,'
						. 'serviceUrl: "' . $Link . '",'
						. 'onSelect: function (worker) '
						. '{'
						. '$("#WorkerContainerNew").html("");'
						. 'var $Data = {WORKER: worker.value, ID:worker.data};'
						. '$("#ajaxworkerTMPL").tmpl($Data).appendTo("#WorkerContainerNew");'
						. '$("#' . $control_name . $name . '_autocomplete").val(""); '
						. '}'
						. '}'
						. ');';
		$JS .= 'var WorkerData = "' . $value . '";'
						. 'if(WorkerData!="")'
						. '{'
						. 'getWorker(WorkerData);'
						. '}';
		Helper::SetJS( $JS );
		return $return;

	}

}
