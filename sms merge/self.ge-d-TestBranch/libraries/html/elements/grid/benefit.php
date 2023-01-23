<?php
require_once PATH_BASE . DS . 'libraries' . DS . 'Units.php';

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
class JGridElementBenefit extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'benefit';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$IDS = (array) explode( '|', C::_( $key, $row ) );

		$Text = [];
		foreach ( $IDS as $id )
		{
			if ( empty( $id ) )
			{
				continue;
			}

			$Text[] = '<div class="key_div">'
							. '<span class="key_val">'
							. $this->get_benefit_types( $id )
							. '</span>'
							. '</div>';
		}

		return '<div class="key_row">' . implode( '', $Text ) . '</div>';

	}

	public function get_benefit_types( $id )
	{
		static $get_benefit_types = null;
		if ( is_null( $get_benefit_types ) )
		{
			$Query = 'select '
							. ' e.id, '
							. ' e.lib_title || \' (\' || e.lib_desc || \')\' as lib_title '
							. ' from lib_f_benefit_types e '
							. ' where '
							. ' e.active = 1 '
							. ' order by e.lib_title';

			$get_benefit_types = DB::LoadObjectList( $Query, 'ID' );
		}

		if ( $id > 0 )
		{
			$data = C::_( $id, $get_benefit_types );
			return C::_( 'LIB_TITLE', $data );
		}

		return $get_benefit_types;

	}

}
