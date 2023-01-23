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
class JGridElementEditLink extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'EditLink';

	public function fetchElement( $row, $node, $config )
	{
		$html = '';
		$editView = $this->GetConfigValue( $config, '_option_edit', 'default' );
		$key = trim( $node->attributes( 'key' ) );
		$translate = trim( $node->attributes( 't' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );
		$Task = C::_( 'task', $config, 'edit' );
		if ( $key )
		{
			$Text = [];
			$input = (array) explode( ', ', $key );
			foreach ( $input as $key )
			{
				$label = $row->{strtoupper( trim( $key ) )};
				if ( $translate == 1 )
				{
					$Text[] = XTranslate::_( $label, $Tscope );
				}
				else
				{
					$Text[] = $label;
				}
			}

			if ( !empty( $Text ) )
			{
				$html .= ' <a href="?option=' . $editView . '&amp;task=' . $Task . '&amp;nid[]=' . $row->ID . '" >';
				$html .= implode( ' ', $Text );
				$html .= ' </a>';
			}
		}
		return $html;

	}

}
