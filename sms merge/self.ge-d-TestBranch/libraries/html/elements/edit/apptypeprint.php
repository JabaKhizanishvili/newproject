<?php
/**
 * @version		$Id: Print.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a Print element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementApptypeprint extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Apptypeprint';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$AppType = $this->getAppTypeList();
		$value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
		return '<strong>: ' . C::_( $value . '.TITLE', $AppType ) . '</strong>';

	}

	public function getAppTypeList()
	{
		static $App = null;
		if ( is_null( $App ) )
		{
			$query = 'select '
							. ' t.id, '
							. ' t.lib_title title  '
							. ' from LIB_LIMIT_APP_TYPES t ';
			$App = DB::LoadObjectList( $query, 'ID' );
		}
		return $App;

	}

}
