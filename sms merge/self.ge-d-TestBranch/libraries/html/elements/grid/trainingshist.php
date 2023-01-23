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
class JGridElementTrainingsHist extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'TrainingsHist';

	public function fetchElement( $row, $node, $group )
	{
		$key = trim( $node->attributes( 'key' ) );
		$html = '';
		$TrainingID = C::_( 'data.training', $group );
		if ( $key )
		{
			if ( isset( $row->{$key} ) )
			{
				$html .= '<div id = "trainingshist' . $row->{$key} . '" data-rel="' . $TrainingID . '" class = "open_detales ' . $key . '_trainingshist">'
								. '<div>'
								. '</div>'
								. '</div>'
				;
			}
		}
		return $html;

	}

}
