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
class JGridElementOrgplaces extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'orgplaces';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$org = trim( $node->attributes( 'org' ) );
		$translate = trim( $node->attributes( 't' ) );

		$key_val = isset( $row->{$key} ) ? $row->{$key} : '';
		$org_val = isset( $row->{$org} ) ? $row->{$org} : '';

		$Select = $this->Select();
		$chiefs = [];
		$label = '';
		$result = '';
		foreach ( $Select as $key => $value )
		{
			if ( $org_val == $value->ORG && $key_val == $value->ORGPID )
			{
				$label = XTranslate::_( $value->ORG_NAME );
				$Text = $value->ORG_PLACE_NAME;
				if ( $translate == 1 )
				{
					$Text = XTranslate::_( $Text );
				}
				$chiefs[] = $Text;
			}
		}
		$result .= '<strong style="color:#00756A;">' . $label . '</strong><br>';
		$result .= '<div class="key_div">'
						. '<span class="key_val">'
						. implode( ', ', $chiefs )
						. '</span>'
						. '</div>';
		return $result;

	}

	public function Select()
	{
		static $result = null;
		if ( is_null( $result ) )
		{
			$query = 'select '
							. ' t.org, '
							. ' t.orgpid, '
							. ' t.org_name, '
							. ' t.org_place_name '
							. ' from hrs_workers_sch t '
							. ' where '
							. ' t.active = 1 '
			;
			$result = DB::LoadObjectList( $query );
		}
		return $result;

	}

}
