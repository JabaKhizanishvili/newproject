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
 * The JElement is the base class for all JElement types
 *
 * @abstract
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElement
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

	public function render( $xmlElement, $value, $control_name = 'params', $Count = 1, $Group = null, $Col = 1 )
	{
		$name = $xmlElement->attributes( 'name' );
		$label = $xmlElement->attributes( 'label' );
		$descr = $xmlElement->attributes( 'description' );
		$must = $xmlElement->attributes( 'must' );
		$hidden = ($xmlElement->attributes( 'type' ) == 'hidden') ? 1 : 0;
		//make sure we have a valid label
		$label = $label ? $label : $name;
		$result[0] = $this->fetchTooltip( $label, $descr, $xmlElement, $control_name, $name );
		$result[1] = $this->fetchElement( $name, $value, $xmlElement, $control_name );
		$result[2] = $descr;
		$result[3] = $label;
		$result[4] = $value;
		$result[5] = $name;
		$result[6] = $must;
		$result[7] = $hidden;
		$result['state'] = $xmlElement->attributes( 'state' );
		$result['depend'] = $xmlElement->attributes( 'depend' );
		$result['count'] = $Count;
		$result['col'] = $Col;
		$result['group'] = $Group;
		return $result;

	}

	public function fetchTooltip( $label, $description, $xmlElement, $control_name = '', $name = '' )
	{
		$output = '<label id="' . $control_name . $name . '-lbl" for="' . $control_name . $name . '"';
		if ( $description )
		{
			$output .= ' title="' . Text::_( $description ) . '">';
		}
		else
		{
			$output .= '>';
		}
		$output .= Text::_( $label ) . '</label>';

		return $output;

	}

	public function fetchElement( $name, $value, $xmlElement, $control_name )
	{
		return;

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
