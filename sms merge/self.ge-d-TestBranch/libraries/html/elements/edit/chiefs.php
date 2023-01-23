<?php

class JElementChiefs extends JElement
{
	var $_name = 'chiefs';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$UserID = $this->_parent->get( 'ID' );
		$ORG = C::_( '_registry._default.data.ORG', $this->_parent );
		$mode = $node->attributes( 'mode' );

		$getchief = 'getChiefs';
		$button = '<button type="button" class="btn btn-danger" onclick="CleanChiefsData(' . $ORG . ');" >' . Text::_( 'Clear' ) . '</button>';
		if ( $mode == 1 )
		{
			$getchief = 'getChief';
			$button = '';
		}
		$Data = $this->getChiefs( $UserID, $ORG );
		if ( $mode != 1 )
		{
			if ( empty( $Data ) )
			{
				$value = '';
			}
			else
			{
				$value = implode( ',', $Data );
			}
		}

		$Link = '?option=s&service=ajaxchiefs&org=' . $ORG;
		$return = '<div class="ChiefsBlock">'
						. '<div class="ChiefsContainer' . $ORG . '"></div>'
						. '<div class="cls"></div>'
						. '<div class="text-right">'
						. $button
						. '</div>'
						. '<div class="input-group">'
						. '<input type="text" id="' . $control_name . $name . '_autocomplete" class="kbd form-control" placeholder="' . Text::_( 'Type Chief Name' ) . '" title="' . Text::_( 'Type Chief Name' ) . '" />'
						. '</div>'
						. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="" class="ChiefsData' . $ORG . '" />'
						. '</div>'
		;
		$JS = '$("#' . $control_name . $name . '_autocomplete").autocomplete('
						. '{'
						. 'minChars:3,'
						. 'autoSelectFirst:true,'
						. 'showNoSuggestionNotice:1,'
						. 'serviceUrl: "' . $Link . '",'
						. 'onSelect: function (worker) '
						. '{ ' . $getchief . '(worker.data , ' . $ORG . ') ;'
						. '$(this).val(""); '
						. '}'
						. '}'
						. ');'
						. '';
		$JS .= 'var ChiefsData' . $ORG . '= "' . $value . '";'
						. 'if(ChiefsData' . $ORG . '!="")'
						. '{ '
						. $getchief . '(ChiefsData' . $ORG . ', ' . $ORG . ');'
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
