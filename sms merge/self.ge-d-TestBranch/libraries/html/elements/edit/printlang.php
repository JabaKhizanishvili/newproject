<?php
/**
 * @version		$Id: printlang.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a printlang element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementprintlang extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'printlang';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$value = C::_( 'LIB_TITLE', $this->getprintlang( $valueIN ) );
		return '<div class="form-control"><strong>' . $value . '</strong></div>';

	}

	public function getprintlang( $worker_id )
	{
		static $printlangs = null;
		if ( is_null( $printlangs ) )
		{
			$query = 'select '
							. ' w.lib_code, '
							. ' w.lib_title '
							. ' from lib_languages w '
							. ' where w.active = 1';
			$printlangs = DB::LoadObjectList( $query, 'LIB_CODE' );
		}
		return C::_( $worker_id, $printlangs, ' - ' );

	}

}
