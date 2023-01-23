<?php

class JElementApplimittype extends JElement
{
	var $_name = 'applimittype';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$options = array();
		if ( !$this->_parent->get( 'ID' ) )
		{
			$Graphs = $this->getStandartGraphs( 0 );
			$options[] = HTML::_( 'select.option', -1, Text::_( 'SELECT CATEGORY' ) );
		}
		else
		{
			$Graphs = $this->getStandartGraphs( $value );
		}

		$html = '<select name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" class="selectpicker form-control"  onchange="setGraphActior(\'' . $control_name . $name . '\');" >';

		$html .= HTMLSelect::Options( $options, 'value', 'text', $value );
		$html .= $this->Options( C::_( 'App', $Graphs ), $value, C::_( 'Org', $Graphs ), $name );
		$html .= '</select>';

		return $html;

	}

	/**
	 * Generates just the option tags for an HTML select list
	 *
	 * @param	array	An array of objects
	 * @param	string	The name of the object variable for the option value
	 * @param	string	The name of the object variable for the option text
	 * @param	mixed	The key that is selected (accepts an array or a string)
	 * @returns	string	HTML for the select list
	 */
	public static function options( $arr, $selected, $orgs, $name )
	{
		$html = '';

		foreach ( (array) $arr as $option )
		{
			$k = C::_( 'ID', $option );
			$t = XTranslate::_( C::_( 'LIB_TITLE', $option ) );
			$id = ( isset( $option->ID ) ? $option->ID : null );
			$org = ' data-rel = "' . implode( '|', C::_( $k, $orgs, [] ) ) . '" ';

			$extra = ( (string) $k == (string) $selected ? ' selected="selected"' : '' );
			$html .= '<option value="' . $k . '" ' . $extra . $org . ' data-replacer="' . C::_( 'REPLACER_FIELD', $option ) . '" data-comment="' . C::_( 'COMMENT_FIELD', $option ) . '" data-files="' . C::_( 'FILES_FIELD', $option ) . '" orgs="' . C::_( 'MYORGS', $option ) . '" >' . $t . '</option>';
		}

		Helper::SetJS( '
		$(document).ready(function(){
		$("#paramsTYPE").change(function(){ 
			checkAllH($(this));
           });
		checkAllH($("#paramsTYPE"));
		function checkAllH(what)
		{
		     checkField(what, "data-replacer", "REPLACING_WORKERS");
                checkField(what, "data-comment", "W_HOLIDAY_COMMENT");
                checkField(what, "data-files", "FILES");
           }
		function checkField(what, attribute, target)
            {
            var value = what.children("option:selected").attr(attribute);
            switch(value)
            {
            case "1":
            $("#form-item-"+target).show();
		 $(".label-must","#form-item-"+target).show();
		 $(".must-mark","#form-item-"+target).show();
            break;
            case "2":
            $(".label-must","#form-item-"+target).hide();
		 $(".must-mark","#form-item-"+target).hide();
            $("#form-item-"+target).show();
            break;
            case "3":
            $(".label-must","#form-item-"+target).show();
		 $(".must-mark","#form-item-"+target).hide();
            $("#form-item-"+target).show();
            break;
            case "4":
            $(".label-must","#form-item-"+target).hide();
		 $(".must-mark","#form-item-"+target).hide();
            $("#form-item-"+target).show();
            break;
            default:
            $("#form-item-"+target).hide();
            }
            }});' );

		Helper::SetJS( '
var reset = $(".' . $name . '");
var dropdown = $("#params' . $name . '");
var params = "paramsORG";

if(reset.length > 0){
reset.children("input").attr("disabled","disabled");
SetOrg($(dropdown));
dropdown.change(function(){ 
 reset.children("input").attr("disabled","disabled");
 SetOrg(this);
});
function SetOrg(target)
{
reset.children("label").hide();
reset.children("br").remove();
var data = $(target).children("option:selected").data("rel")+"";
data = data.split("|");
data.forEach(function(x){
if(x > 0)
{
 var id = params + x;
 $("#"+id).show().attr("disabled", false);
 $("label[for = "+id+"]").show().after("<br>");
}
});
}
}
'
		);

		return $html;

	}

	public function getStandartGraphs( $mode )
	{
		$myorgs = XGraph::GetMyOrgsIDx();
		if ( empty( $myorgs ) )
		{
			return [];
		}

		$where = ' re.org in (' . implode( ',', $myorgs ) . ')';
		$where .= ' and (p.active > 0) order by p.lib_title asc';
		if ( $mode > 0 )
		{
			$where = 'p.id = ' . DB::Quote( $mode );
		}
		$Query = 'select '
						. ' p.*,'
						. ' re.org,'
						. ' p.lib_title, p.lib_desc from lib_limit_app_types p  '
						. ' left join rel_limit_app_types re on re.limit_app_type = p.id '
						. ' where '
						. $where;
		$results = DB::LoadObjectList( $Query );

		$last = [];
		$last['App'] = [];
		$last['Org'] = [];
		foreach ( $results as $result )
		{
			$id = C::_( 'ID', $result );
			$last['App'][$id] = $result;
			$last['Org'][$id] = C::_( $id, $last['Org'], [] );
			$last['Org'][$id][] = C::_( 'ORG', $result );
		}
		return $last;

	}

}
