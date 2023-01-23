<?php
require_once PATH_BASE . DS . 'libraries' . DS . 'Units.php';

/**
 * @version		$Id: list.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
/**
 * Renders a list element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementorgunitnew extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'orgunitnew';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$link = '?';
		$width = $node->attributes( 'width', '95%' );
		$height = $node->attributes( 'height', '95%' );
		$ORG = C::_( '_registry._default.data.ORG', $this->_parent );
		$uri = URI::getInstance( $link );
		$uri->setVar( 'option', 'orgunits' );
		$uri->setVar( 'tmpl', 'modal' );
		$uri->setVar( 'groups', '0' );
		$uri->setVar( 'org', $ORG );
		$uri->setVar( 'js', 'getOrgUnit' );
		$uri->setVar( 'jsvar[]', $ORG );
		$uri->setVar( 'width', $width );
		$uri->setVar( 'iframe', 'true' );
		$uri->setVar( 'height', $height );
		$return = '<div class="OrgUnitsBlock">
        <div class="OrgUnitContainer' . $ORG . '"></div>
        <div class="cls"></div>
        <div class="OrgUnitsButtons">'
						. '<a class="btn btn-primary" rel="iframe-' . $ORG . '" href="' . $uri->toString() . '">'
						. Text::_( 'Select' )
						. '</a>
            <div class="cls"></div>
            <input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="" class="OrgUnitData' . $ORG . '" />
        </div>
    </div>';
		$JS = '$("a[rel^=\'iframe-' . $ORG . '\']").prettyPhoto();';
		$JS .= 'var OrgUnitData' . $ORG . ' = "' . $value . '";'
						. 'if(OrgUnitData' . $ORG . '!="")'
						. '{'
						. 'getOrgUnit(OrgUnitData' . $ORG . ', "' . $ORG . '");'
						. '}';
		Helper::SetJS( $JS );
		return $return;

	}

}
