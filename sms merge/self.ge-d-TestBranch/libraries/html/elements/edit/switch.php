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
class JElementSwitch extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Switch';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Yes = Text::_( $node->attributes( 'yes' ) );
		$No = Text::_( $node->attributes( 'no' ) );
		$Checked = '';
		if ( $value == 1 )
		{
			$Checked = ' checked="checked" ';
		}
		if ( !defined( 'ON_OFF_SWITCH' ) )
		{
			define( 'ON_OFF_SWITCH', 1 );
			Helper::SetJS( 'new DG.OnOffSwitchAuto({
        cls:".custom-switch",
        textOn:"' . $Yes . '",
        textOff:"' . $No . '"
    });' );
		}
		return '<input type="checkbox" name="' . $control_name . '[' . $name . ']' . '" value="1" ' . $Checked . ' class = "skip_this custom-switch" id="' . $control_name . $name . '"/> ';
//		return HTML::_( 'select.radiolist', $options, '' . $control_name . '[' . $name . ']', ' class = "skip_this" ', 'value', 'text', $value, $control_name . $name );

	}

}
