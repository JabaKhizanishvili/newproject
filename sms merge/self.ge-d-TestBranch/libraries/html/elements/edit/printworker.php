<?php
/**
 * @version		$Id: Print.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a Print element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementPrintworker extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'printworker';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
		if ( (int) $value > 0 )
		{
			$wData = XGraph::getWorkerDataSch( $value, 1 );
			$value = XTranslate::_( C::_( 'FIRSTNAME', $wData, '' ) ) . ' ' . XTranslate::_( C::_( 'LASTNAME', $wData, '' ) ) . ' - ' . XTranslate::_( C::_( 'SCHEDULE_NAME', $wData, '' ) ) . ' - ' . XTranslate::_( C::_( 'ORG_NAME', $wData, '' ) );
		}
		return '<div class="form-control form_field" style="height: unset;min-height: 40px !important;"><strong>' . $value . '</strong></div>';

	}

}
