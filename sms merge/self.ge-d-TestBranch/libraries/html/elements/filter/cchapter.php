<?php

/**
 * @version		$Id: list.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
/**
 * Renders a list element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class FilterElementCChapter extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'CChapter';

	public function fetchElement( $name, $ChapterID, $node, $config )
	{
		$app = array( '0' => Text::_( 'Chapter Filter' ) );
		$List = self::getChapterList();
		$options = array();
		foreach ( $app as $val => $text )
		{
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		foreach ( $List as $item )
		{
			$val = $item->SID;
			$text = $item->LIB_TITLE;
			$options[] = HTML::_( 'select.option', $val, $text, 'value', 'text' );
		}
		$value = $this->GetConfigValue( $config['data'], $name );
		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control search-select" ', 'value', 'text', $value, $ChapterID );

	}

	public static function getChapterList()
	{
		static $Chapters = null;
		if ( is_null( $Chapters ) )
		{
			$Query = 'select '
							. ' c.sid, '
							. ' c.lib_title '
							. ' from ('
							. ' select '
							. ' t.chapter '
							. ' from slf_persons t '
							. ' left join lib_roles r on r.id = t.user_role '
							. ' WHERE '
							. ' t.id in ('
							. ' select '
							. ' rw.worker '
							. ' from REL_WORKER_CHIEF rw '
							. ' where '
							. ' rw.chief in (select m.id from hrs_workers m where m.PARENT_ID =  ' . DB::Quote( Users::GetUserID() )
							. ' )) group by t.chapter) k '
							. ' left join lib_chapter c on c.sid = k.chapter order by c.lib_title asc';
			$Chapters = DB::LoadObjectList( $Query, 'SID' );
		}
		return $Chapters;

	}

}
