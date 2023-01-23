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
class JElementPersonprint extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'personprint';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$person = $this->getPerson( $valueIN );
		$person_name = XTranslate::_( C::_( 'FIRSTNAME', $person ) ) . ' ' . XTranslate::_( C::_( 'LASTNAME', $person ) );
		echo '<input type="hidden" name="params[' . $name . ']" id="params' . $name . '" value="' . $valueIN . '">';
		return '<div class="form-control"><strong>' . $person_name . '</strong></div>';

	}

	public function getPerson( $valueIN )
	{
		if ( empty( $valueIN ) )
		{
			return [];
		}
		$Query = 'select x.firstname, x.lastname from slf_persons x where x.active > 0 and x.id = ' . DB::Quote( $valueIN );
		return DB::LoadObject( $Query );

	}

}
