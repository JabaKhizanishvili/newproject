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
class JElementPrintastext extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'printastext';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$html = '<div class="form-control noheight">';
		if ( $Data = json_decode( $valueIN ) )
		{
			$C = 1;
			foreach ( $Data as $Item )
			{
				$K = array();
				foreach ( $Item as $V )
				{
					$K[] = $V;
				}
				$html .= '<strong>' . $C . ') ' . implode( ' - ', $K ) . '</strong><br />';
				$C++;
			}
		}
		else
		{
			$value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
			$html .= '<strong>' . $value . '</strong>';
		}
		$html .= '</div>';
		return $html;

	}

}
