<?php

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
class FilterElementSwitch extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Switch';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$Yes = Text::_( $node->attributes( 'yes', 'Yes' ) );
		$No = Text::_( $node->attributes( 'no', 'No' ) );
		$Checked = '';
		$Checked2 = '';
		if ( $value == 1 )
		{
			$Checked = ' checked="checked" ';
		}
		else
		{
			$Checked2 = ' checked="checked" ';
			
		}
		
		Helper::SetJS( 'new DG.OnOffSwitchAuto({
		cls:"#' . $id . '",
		textOn:"' . $Yes . '",
		textOff:"' . $No . '",
		listener:function(name, checked){
	//	setInterval("setFilter()",( 500));
        }
    });' );
		$HTML = '<input type="checkbox" name="' . $name . '[]" value="1" ' . $Checked . ' class = "skip_this custom-switch" id="' . $id . '"/> ';
		$HTML .= '<input type="hidden" name="' . $name . '[]" value="0" ' . $Checked2 . ' />';
		return $HTML;

	}

}
