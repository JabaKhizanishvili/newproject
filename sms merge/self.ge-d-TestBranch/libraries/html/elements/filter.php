<?php
/**
 * @version		$Id: element.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Parameter base class
 *
 * The JGridElement is the base class for all JGridElement types
 *
 * @abstract
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class FilterElement
{
	/**
	 * element name
	 *
	 * This has to be set in the final
	 * renderer classes.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = null;

	/**
	 * reference to the object that instantiated the element
	 *
	 * @access	protected
	 * @var		object
	 */
	protected $_parent = null;

	/**
	 * Constructor
	 *
	 * @access protected
	 */
	public function __construct( $parent = null )
	{
		$this->_parent = $parent;

	}

	/**
	 * get the element name
	 *
	 * @access	public
	 * @return	string	type of the parameter
	 */
	public function getName()
	{
		return $this->_name;

	}

	public function render( $name, $id, $node, $config )
	{
		echo $this->fetchElement( $name, $id, $node, $config );

	}

	public function fetchElement( $name, $id, $node, $config )
	{
		return;

	}

	public function GetConfigValue( $config, $key, $default = '' )
	{
		if ( is_object( $config ) )
		{
			$Value = isset( $config->{$key} ) ? $config->{$key} : $default;
		}
		else
		{
			$Value = isset( $config[$key] ) ? $config[$key] : $default;
		}
		return $Value;

	}

	public function SetGeoKBD( $node, $id )
	{
		/* @var $node SimpleXMLElements  */
		$geokbd = $node->attributes( 'geokbd', false );
		if ( $geokbd !== false )
		{
			Helper::SetJS( '$("#' . $id . '").geokbd({on:' . $geokbd . '});' );
		}

	}

}
