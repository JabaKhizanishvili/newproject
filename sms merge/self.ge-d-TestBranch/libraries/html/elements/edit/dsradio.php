<?php
/**
 * @version		$Id: radio.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a radio element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementDSRadio extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'dsradio';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$options = array();
		foreach ( $node->children() as $option )
		{
			$val = $option->attributes( 'value' );
			$text = $option->data();
			$options[] = HTML::_( 'select.option', $val, Text::_( $text ) );
		}
		echo '<pre>';
		print_r($node);
		echo '</pre>';
//		die();
		Helper::SetJS( 'setTrainigType();' );
		return HTML::_( 'select.radiolist', $options, '' . $control_name . '[' . $name . ']', ' onclick="setTrainigType();" ', 'value', 'text', $value, $control_name . $name );

	}

}
