<?php
/**
 * @version        $Id: list.php 1 2011-07-13 05:09:23Z $
 * @package    WSCMS.Framework
 * @copyright    Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a list element
 *
 * @package    WSCMS.Framework
 * @subpackage        Parameter
 * @since        1.5
 */
class JGridElementAttributeList extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access    protected
	 * @var        string
	 */
	protected $_name = 'attributelist';

	public function fetchElement( $row, $node, $group )
	{
		$attributeIds = $row->ATTRIBUTES;
		if ( empty( $attributeIds ) )
		{
			return '';
		}
		$query = "SELECT la.LIB_TITLE FROM lib_attributes la WHERE la.active = 1 AND la.id in (" . $attributeIds . ")";

		$attributes = DB::LoadObjectList( $query );

		$attrs = [];

		foreach ( $attributes as $attribute )
		{
			$attrs[] = XTranslate::_( $attribute->LIB_TITLE );
		}

		return implode( ', ', $attrs );

	}

}
