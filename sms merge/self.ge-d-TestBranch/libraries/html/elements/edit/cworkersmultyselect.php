<?php

class JElementCworkersmultyselect extends JElement
{
	var $_name = 'Cworkersmultyselect';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		if ( Helper::CheckTaskPermision( 'admin' ) )
		{
			return $this->Cworkers( $name, $value, $node, $control_name );
		}
		else
		{
			$class = ' class="form-control multySelector"';
			$size = ( $node->attributes( 'size' ) ? ' size="' . $node->attributes( 'size' ) . '" ' : ' size="10" ' );
			$Depts = $this->getLibList();
			$options = array();
			foreach ( $Depts as $option )
			{
				$val = C::_( 'ID', $option );
				$text = C::_( 'TITLE', $option );
				$options[] = HTML::_( 'select.option', $val, Text::_( $text ) );
			}
			return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . '][]', $class . $size . ' multiple ', 'value', 'text', $value, $control_name . $name );
		}

	}

	public function Cworkers( $name, $value, $node, $control_name )
	{
		$Link = '?option=s&service=ajaxworkers';
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
						. 'if(WorkersData!="")'
						. '{'
						. 'getWorkers(WorkersData);'
						. '}';
		Helper::SetJS( $JS );
		return $return;

	}

	protected function getLibList()
	{
		$query = 'select '
						. ' t.id, '
						. ' t.firstname || \' \' || t.lastname title '
						. ' from slf_persons t '
						. ' where '
						. ' t.active = 1 '
						. ' and t.id in (select wc.worker from rel_worker_chief wc where wc.chief =  ' . Users::GetUserID() . ' ) '
						. ' order by title asc';
		return DB::LoadObjectList( $query );

	}

}
