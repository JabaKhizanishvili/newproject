<?php
/**
 * @version		$Id: spacer.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a spacer element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementSpacer extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Spacer';

	public function fetchTooltip( $label, $description, $node, $control_name = '', $name = '' )
	{
		return '';

	}

	public function fetchElement( $name, $value, $node, $control_name )
	{
		if ( $value )
		{
			return '<div class="items_spacer">' . Text::_( $value ) . '</div>';
		}
		else
		{
			return '<hr />';
		}

	}

}
