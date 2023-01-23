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
class JGridElementRegion extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Regions';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
        $regions = GIS::getLocationList();
        
        $Value = C::_( $key, $row );
        $regionsArray = explode(",", $Value);
        //print_r($regionsArray);
        $regionTitleText = '';

        foreach( $regionsArray as $regionId ) {
            $regionTitleArray[] = C::_( $regionId . '.Name', $regions );
        }
        return implode(", ", $regionTitleArray);

	}

	
}
