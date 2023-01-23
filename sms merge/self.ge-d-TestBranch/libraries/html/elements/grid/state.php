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
class JGridElementState extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'State';

	public function fetchElement( $row, $node, $config )
	{
		$Statekey = trim( $node->attributes( 'state_key' ) );
		$StateID = trim( $node->attributes( 'state_id' ) );
		$Task = trim( $node->attributes( 'task', 'changestate' ) );
		return HTML::Status( $row->{$Statekey}, $row->{$StateID}, $this->GetConfigValue( $config, '_option_edit' ), $Task );

	}

}
