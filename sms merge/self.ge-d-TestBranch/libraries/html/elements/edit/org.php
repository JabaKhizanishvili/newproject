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
class JElementOrg extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Org';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$limit = $node->attributes( 'limit' );
		if ( $limit == '1' )
		{
			$Depts = XGraph::GetMyOrgs();
		}
		elseif ( $limit == '2' )
		{
			$Depts = self::getPrivateTimeOrgList();
		}
		else
		{
			$Depts = Units::getOrgList();
		}
		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Org Place' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			if ( $limit == '1' )
			{
				$text = XTranslate::_( $dept->LIB_TITLE );
			}
			else
			{
				$text = XTranslate::_( $dept->TITLE );
			}
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name );

	}

	public static function getPrivateTimeOrgList()
	{
		$OrgsON = Helper::CleanArray( explode( '|', Helper::getConfig( 'private_date_orgs' ) ) );
		$UserID = Users::GetUserID();
		$Q = ' select '
						. ' re.org id, '
						. ' u.lib_title TITLE '
						. ' from '
						. ' hrs_workers re '
						. ' left join lib_unitorgs u on u.id = re.org '
						. ' where '
						. ' re.parent_id = ' . DB::Quote( $UserID )
						. ' and u.active = 1 '
						. ' and re.active = 1 '
						. ' and re.org in (' . implode( ',', $OrgsON ) . ') '
		;
		return DB::LoadObjectList( $Q );

	}

}
