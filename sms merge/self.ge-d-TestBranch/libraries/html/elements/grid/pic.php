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
class JGridElementPic extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Pic';

	public function fetchElement( $row, $node, $group )
	{
		$URL = 'templates/images/user.png';
		$key = trim( $node->attributes( 'key' ) );
		$Def = trim( $node->attributes( 'def' ) );
		if ( $Def == '' )
		{
			$Def = 1;
		}
		$Class = trim( $node->attributes( 'class' ) );
		if ( $key )
		{
			echo '<div class="worker_photo text-center">';
			$Photo = C::_( $key, $row );
			if ( $Photo )
			{
				$URL = X_PATH_TMP_URL . 'APPImages/' . $Photo . '.jpg?t=' . time();
				echo '<a href="' . $URL . '" data-lity >';
				echo HTML::image( $URL, 'Photo', array( 'class' => $Class ) );
				echo '</a>';
			}
			elseif ( $Def > 0 )
			{
				echo HTML::image( $URL, 'Photo', array( 'class' => $Class ) );
			}
			echo '</div>';
		}

	}

}
