<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class TranslationsModel extends Model
{
	/**
	 *
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->edit_text = trim( Request::getState( $this->_space, 'edit_text', '' ) );
		$Return->lib_from = trim( Request::getState( $this->_space, 'lib_from', '' ) );
		$Return->lib_to = trim( Request::getState( $this->_space, 'lib_to', '' ) );
		$Return->from_text = trim( Request::getState( $this->_space, 'from_text', '' ) );
		$Return->to_text = trim( Request::getState( $this->_space, 'to_text', '' ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );

		$where = array();

		if ( $Return->lib_from )
		{
			$where[] = ' w.lib_from = ' . DB::Quote( $Return->lib_from );
		}
		if ( $Return->lib_to )
		{
			$where[] = ' w.lib_to = ' . DB::Quote( $Return->lib_to );
		}
		if ( $Return->edit_text )
		{
			$where[] = ' w.edit_text like ' . DB::Quote( '%' . $Return->edit_text . '%' );
		}
		if ( $Return->from_text )
		{
			$where[] = ' w.from_text like ' . DB::Quote( '%' . $Return->from_text . '%' );
		}
		if ( $Return->to_text )
		{
			$where[] = ' w.to_text like ' . DB::Quote( '%' . $Return->to_text . '%' );
		}

		$where[] = ' w.active = 1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$countQuery = 'select count(*) from hrs_translations w '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' w.id, '
						. ' w.from_text, '
						. ' w.to_text, '
						. ' w.edit_text, '
						. ' w.active, '
						. ' t.lib_title LIB_FROM, '
						. ' l.lib_title LIB_TO '
						. ' from hrs_translations w '
						. ' LEFT JOIN lib_languages t ON t.lib_code = w.LIB_FROM  '
						. ' LEFT JOIN lib_languages l ON l.LIB_CODE = w.LIB_TO  '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->items = DB::LoadObjectList( $Limit_query );

		return $Return;

	}

	function getSearch( $set = 0 )
	{
		$Return = $this->getReturn();
		if ( $set != 1 )
		{
			return $Return;
		}

		$Return->target_lng = trim( Request::getState( $this->_space, 'target_lng', '' ) );
		$Return->term = trim( Request::getState( $this->_space, 'term', '' ) );
		$Return->search_type = (int) trim( Request::getState( $this->_space, 'search_type', '0' ) );

		if ( $Return->target_lng && $Return->term )
		{
			$Return->items = $this->GetTranslateTerms( $Return->target_lng, $Return->term, $Return->search_type );
		}

		return $Return;

	}

	/**
	 * Search In Folder
	 */
	function GetTranslateTerms( $TargetLanguage, $Phrase, $Type, $Count = 20 )
	{
		if ( $Type == 1 )
		{
			$Search = '\'"output":"' . $Phrase . '"\'';
		}
		else
		{
			$Search = '\'' . $Phrase . '\'';
		}
		$Command = 'grep -iRrl ' . $Search . ' ' . X_PATH_BUFFER . DS . 'Translates' . DS . $TargetLanguage . DS;
		$Output = [];
		exec( $Command, $Output );
		$Data = [];

		$Result = array_slice( $Output, 0, $Count );
		foreach ( $Result as $File )
		{
			$Data[] = json_decode( file_get_contents( $File ) );
		}
		return $Data;

	}

}
