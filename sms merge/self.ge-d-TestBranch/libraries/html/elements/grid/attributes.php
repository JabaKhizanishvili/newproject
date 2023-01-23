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
class JGridElementAttributes extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access    protected
	 * @var        string
	 */
	protected $_name = 'attributes';

	public function fetchElement( $row, $node, $group )
	{
		$length = trim( $node->attributes( 'length' ) );
		$sp = trim( $node->attributes( 'word-separator' ) );
		$limit_type = trim( $node->attributes( 'limit_type' ), 0 );
		$attributeItemType = trim( $node->attributes( 'item-type' ) );
		$key = trim( $node->attributes( 'key' ) );
		$Value = C::_( $key, $row, null );
		if ( empty( $Value ) )
		{
			return '';
		}
		$Items = Helper::CleanArray( explode( ',', $Value ) );
		$Attributes = $this->getAttributes( $attributeItemType );
		$Return = [];
		$translate = trim( $node->attributes( 't' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );
		foreach ( $Items as $Item )
		{
			$ItemData = C::_( $Item, $Attributes );
			if ( empty( $ItemData ) )
			{
				continue;
			}
			if ( $translate )
			{
				$Title = XTranslate::_( C::_( 'LIB_TITLE', $ItemData ), $Tscope );
			}
			else
			{
				$Title = C::_( 'LIB_TITLE', $ItemData );
			}

			$Return[] = '<tag style="color: ' . C::_( 'COLOR', $ItemData, '#ffffff' ) . ';">' . $Title . '</tag>';
		}
		$toolTipStyles = [ 'width: 400px' ];

		return Helper::MakeToolTip( implode( ', ', $Return ), $length, $limit_type, $sp, $toolTipStyles );

	}

	public function getAttributes()
	{
		static $Attributes = null;
		if ( is_null( $Attributes ) )
		{
			$Query = 'SELECT '
							. ' la.ID, '
							. ' la.LIB_TITLE, '
							. ' la.COLOR FROM LIB_ATTRIBUTES la '
							. ' WHERE '
							. ' la.active = 1 '
			;
			$Attributes = XRedis::getDBCache( 'lib_attributes', $Query, 'LoadObjectList', 'ID' );
		}

		return $Attributes;

	}

}
