<?php

class JElementSlfstaff extends JElement
{
	var $_name = 'slfstaff';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$case = $node->attributes( 'case' );
		$mode = $node->attributes( 'tmpl' );
		if ( empty( $mode ) )
		{
			$mode = 'multy';
		}

		if ( is_array( $value ) )
		{
			$value = implode( ',', $value );
		}

		$org = '';
		switch ( $case )
		{
			default:
			case 1: // Get All Persons
				$org = '';
				break;
			case 2: // Get Organization Workers
				$org = C::_( '_registry._default.data.ORG', $this->_parent );
				if ( !$org )
				{
					$org = (int) trim( Request::getState( '.display', 'org', '' ) );
				}
				break;
			case 3: // Get Workers from My Organizations
				$org = implode( ',', XGraph::GetMyOrgsIDx() );
				break;
			case 4: // Get My Workers
// Progress...
				break;
			case 5: // Get All Workers
				break;
			case 6: // Get Worker of Organization
				break;
			case 7: // Get Workers by Organizations
				break;
			case 8: // Decide by admin satsk - Workers by Organizations
				if ( !Helper::CheckTaskPermision( 'admin' ) )
				{
					$org = C::_( '_registry._default.data.ORG', $this->_parent );
					if ( !$org )
					{
						$org = (int) trim( Request::getState( '.display', 'org', '' ) );
						}
					$Xhelp_Method = 'ChiefWorkers';
					$Xhelp_param = $org;
					return $this->MultyChosen( $name, $control_name, $value, $Xhelp_Method, $Xhelp_param );
				}
				else
				{
					$case = 7;
					$mode = 'single';
				}
				break;
			case 9: // Decide by admin satsk - Workers by schedule (with multy selector)
				if ( !Helper::CheckTaskPermision( 'admin' ) )
				{
					$org = C::_( '_registry._default.data.ORG', $this->_parent );
					if ( !$org )
					{
						$org = (int) trim( Request::getState( '.display', 'org', '' ) );
					}
					$dataIn = Xhelp::ChiefWorkersSchedule( $org );
					return $this->MyltySelector( $name, $value, $node, $control_name, $dataIn );
				}
				else
				{
					$case = 5;
				}
				break;
			case 10: // Decide by admin satsk - Workers by schedule (with dropdown selector)
				if ( !Helper::CheckTaskPermision( 'admin' ) )
				{
					$org = C::_( '_registry._default.data.ORG', $this->_parent );
					if ( !$org )
					{
						$org = (int) trim( Request::getState( '.display', 'org', '' ) );
					}
					$dataIn = Xhelp::ChiefWorkersSchedule( $org );
					return $this->MultyChosen( $name, $control_name, $value, '', '', $dataIn );
				}
				else
				{
					$case = 5;
				}
				break;
		}

		return $this->ServiceQuery( $name, $control_name, $value, $case, $org, $mode );

	}

	public function MultyChosen( $name, $control_name, $value, $Xhelp_Method, $Xhelp_param, $Depts = null )
	{
		if ( is_null( $Depts ) )
		{
			$Depts = Xhelp::$Xhelp_Method( $Xhelp_param );
		}

		$options[] = HTML::_( 'select.option', 0, Text::_( 'Workers FILTER' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = $dept->TITLE;
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		$html = HTML::_( 'select.genericlist', $options, $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name );
		return $html;

	}

	public function ServiceQuery( $name, $control_name, $value, $case, $org, $mode = '' )
	{
		$Link = '?option=s&service=slfstaff&case=' . $case . '&org=' . $org . '&tmpl=' . $mode;
		$clean = '';
		if ( $mode == 'single' )
		{
			$clean = ' $(".WorkersContainer' . $name . '").html(""); ';
		}
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
						. $clean
						. ' Get_SlfStaff(worker.data , "' . $case . '","' . $name . '","' . $mode . '");'
						. '$("#' . $control_name . $name . '_autocomplete").val(""); '
						. '}'
						. '}'
						. ');'
						. '';
		$JS .= 'var WorkersData = "' . $value . '";'
						. 'if(WorkersData !="")'
						. '{'
						. ' Get_SlfStaff(WorkersData, "' . $case . '","' . $name . '","' . $mode . '");'
						. '}';
		Helper::SetJS( $JS );
		return $return;

	}

	public function MyltySelector( $name, $value, $node, $control_name, $dataIn = [] )
	{
		if ( $value )
		{
			$value = explode( ',', $value );
		}
		$class = ( $node->attributes( 'class' ) ? ' class="' . $node->attributes( 'class' ) . '" ' : ' class="form-control multySelector" ' );
		$size = 'size="10"';
		$options = array();
		if ( count( $dataIn ) > 0 )
		{
			foreach ( $dataIn as $option )
			{
				$val = $option->ID;
				$text = $option->TITLE;
				$options[] = HTML::_( 'select.option', $val, Text::_( $text ) );
			}
		}

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . '][]', $class . $size . ' multiple ', 'value', 'text', $value, $control_name . $name );

	}

}
