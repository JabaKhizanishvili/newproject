<?php

class JElementgraphgroup extends JElement
{
	var $_name = 'graphgroup';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$ID = (int) $this->_parent->get( 'PARENT_ID' );		
		$ORG = C::_( '_registry._default.data.ORG', $this->_parent );
		$GroupID = $this->getGroupID( $ID );		
		if ( $value != $GroupID )
		{
			$value = $GroupID;
		}
		$Groups = $this->getGroups( $ORG );
		$options = array();
		$options[] = HTML::_( 'select.option', -1, Text::_( 'Select GRoup' ) );
		foreach ( $Groups as $Group )
		{
			$val = $Group->ID;
			$text = $Group->LIB_TITLE;
			if ( !empty( $Group->LIB_DESC ) )
			{
				$text .= ' (' . $Group->LIB_DESC . ')';
			}
			$options[] = HTML::_( 'select.option', $val, $text );
		}
//		$js = '$(\'#' . $control_name . $name . '\').chosen();';
//		Helper::SetJS( $js );
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getGroupID( $ID )
	{
		$Query = 'select t.group_id from REL_WORKERS_GROUPS t where t.worker = ' . $ID;
		return DB::LoadResult( $Query );

	}

	public function getGroups( $ORG )
	{
		$Query = 'select '
						. ' t.id, '
						. ' t.lib_title, '
						. ' t.lib_desc '
						. ' from LIB_WORKERS_GROUPS t '
						. ' where '
						. ' t.active > -1 '
						.' and t.org = '. (int) $ORG
						. ' order by t.lib_title asc';
		return DB::LoadObjectList( $Query );

	}

}
