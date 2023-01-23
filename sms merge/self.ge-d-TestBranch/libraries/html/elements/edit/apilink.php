<?php
/**
 * @version		$Id: ApiLink.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a ApiLink element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementApiLink extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'ApiLink';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$BaseURL = URI::base();
		$value = $BaseURL . htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
		return ''
						. '<div class="form-control form_field apilink-data" id="' . $name . $control_name . '">'
						. '<strong>'
						. $value
						. '</strong>'
						. '<i class="bi bi-files" onclick="CopyText(\'' . $name . $control_name . ' strong\');"></i>'
						. '</div>'
		;

	}

}
