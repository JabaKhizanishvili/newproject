<?php

class JElementNewChief extends JElement
{
	var $_name = 'newchief';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$UserID = $this->_parent->get( 'PARENT_ID' );		
		$ORG = C::_( '_registry._default.data.ORG', $this->_parent );
		$Data = $this->getChiefs( $UserID, $ORG );
		if ( empty( $Data ) )
		{
			$value = '';
		}
		else
		{
			$value = implode( ',', $Data );
		}
		$Link = '?option=s&service=ajaxchiefs&org=' . $ORG;
		$WLink = '?option=s&service=ajaxwokerschiefs&org=' . $ORG;
		$return = '<div class="ChiefsBlock">'
						. '<div class="ChiefsContainer"></div>'
						. '<div class="cls"></div>'
						. '<div class="text-right">'
//						. '<button type="button" class="btn btn-danger" onclick="CleanChiefsData();" >' . Text::_( 'Clear' ) . '</button>'
						. '</div>'
						. '<div class="input-group">'
						. '<input type="text" name="" id="' . $control_name . $name . '_autocomplete" class="kbd form-control" placeholder="' . Text::_( 'Type Chief Name' ) . '" title="' . Text::_( 'Type Chief Name' ) . '" />'
						. '</div>'
						. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="" class="ChiefsData" />'
						. '</div>'
		;
		$JS = '$("#' . $control_name . $name . '_autocomplete").autocomplete('
						. '{'
						. 'minChars:3,'
						. 'autoSelectFirst:true,'
						. 'showNoSuggestionNotice:1,'
						. 'serviceUrl: "' . $Link . '",'
						. 'onSelect: function (worker) '
						. '{'
						. ' getChiefs(worker.data , ' . $ORG . ') ;'
						. '$(this).val(""); '
						. '}'
						. '}'
						. ');'
						. '';
		$JS .= '$("#' . $control_name . $name . '_autocompleteworkers").autocomplete('
						. '{'
						. 'minChars:3,'
						. 'autoSelectFirst:true,'
						. 'showNoSuggestionNotice:1,'
						. 'serviceUrl: "' . $WLink . '",'
						. 'onSelect: function (worker) '
						. '{'
						. ' getChiefs(worker.data, ' . $ORG . ' ) ;'
						. '$(this).val(""); '
						. '}'
						. '}'
						. ');'
						. '';
		$JS .= 'var ChiefsData = "' . $value . '";'
						. 'if(ChiefsData!="")'
						. '{'
						. 'getChiefs(ChiefsData, ' . $ORG . ');'
						. '}';
		Helper::SetJS( $JS );
		return $return;

	}

	public function getChiefs( $UserID, $ORG )
	{
		if ( empty( $UserID ) )
		{
			return array();
		}

		$Query = 'select t.chief from REL_WORKER_CHIEF t '
						. ' where '
						. ' t.worker=' . (int) $UserID
						. ' and t.org = ' . (int) $ORG
		;
		return DB::LoadList( $Query );

	}

}
