<?php

class JElementBenefitsPrintData extends JElement
{
	var $_name = 'benefitsprintdata';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$exclude = $node->attributes( 'selfexclude' );
		$printdata = $node->attributes( 'printdata' );
		$ORG = $this->_parent->get( 'ORG' );
		$Orgs = Units::getOrgList();

		if ( !$ORG )
		{
			$ORG = (int) trim( Request::getState( '.display', 'org', '' ) );
		}

//		$Graphs = $this->getSchedules( $value, $ORG, $exclude );
		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'SELECT CATEGORY' ) );
		foreach ( $Graphs as $Graph )
		{
			$val = $Graph->ID;
			$text = $Graph->LIB_TITLE;
			$ULevel = $Graph->ULEVEL;
			if ( !empty( $Graph->LIB_DESC ) )
			{
				$text .= '  (' . $Graph->LIB_DESC . ') ';
			}
			if ( !empty( $Graph->MAINUNIT ) )
			{
				$text .= ' - ' . $Graph->MAINUNIT;
			}
			if ( !empty( $Graph->UNIT ) )
			{
				$text .= ' - ' . $Graph->UNIT;
			}
			$options[] = HTML::_( 'select.option', $val, str_repeat( '- ', $ULevel ) . $text );
		}
		Helper::SetJS( 'setGraphActior(\'' . $control_name . $name . '\');' );
		$return = HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="selectpicker form-control search-select ' . $control_name . '"  onchange="setGraphActior(\'' . $control_name . $name . '\');" ', 'value', 'text', $value, $control_name . $name );
//		if ( $printdata )
//		{
//			$return .= '<div id="' . $control_name . $name . '_data" class="noheight schedule"></div>';
//			$Link = '?option=s&service=staffschedule';
//			Helper::SetJS(
//							'var idi = $(".' . $control_name . '").val();'
//							. 'if(idi)'
//							. '{printData(idi);}'
//							. '$(".' . $control_name . '").change(function(){'
//							. 'var id = $(this).val();'
//							. 'printData(id);'
//							. '});'
//							. 'function printData(id){'
//							. ' if(id > 0){'
//							. '$.ajax({
//                                            url: "' . $Link . '",
//                                            type: "POST",
//                                            data: { "schedule": id},
//                                            success: function(echo){'
//							. ' $("#' . $control_name . $name . '_data").html(echo); '
//							. ' $("#' . $control_name . $name . '_data").addClass("form-control"); '
//							. '}});'
//							. '}else{'
//							. ' $("#' . $control_name . $name . '_data").removeClass("form-control").empty(); '
//							. '}'
//							. '}'
//			);
//		}
		return $return;

	}

}
