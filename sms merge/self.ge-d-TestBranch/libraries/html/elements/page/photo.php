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
class PageElementPhoto extends PageElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Photo';

	public function fetchElement( $row, $node, $group )
	{
		$URL = 'templates/images/user.png';
		$key = trim( $node->attributes( 'key' ) );
		$Class = trim( $node->attributes( 'class' ) );
		if ( $key )
		{
			echo '<div class="worker_photo">';
			$Photo = C::_( $key, $row );
			if ( $Photo )
			{

				$dif = explode( ';', $Photo );
				$photo0 = C::_('0', $dif);
				$photo1 = C::_('1', $dif);

				if($photo0){
					$URL = URL_UPLOAD . '/' . $photo0 . '?t=' . time();
					echo '<a href="' . $URL . '" class="photo_zoom" target="_blank" >';
					echo HTML::image( $URL, 'Photo', array( 'class' => $Class ) );
					echo '</a>';
				}
				
				if($photo1){
					$URL = URL_UPLOAD . '/' . $photo1 . '?t=' . time();
					echo '<a href="' . $URL . '" class="photo_zoom" target="_blank" >';
					echo HTML::image( $URL, 'Photo', array( 'class' => $Class ) );
					echo '</a>';
				}
				
			}
			else
			{
				echo HTML::image( $URL, 'Photo', array( 'class' => $Class ) );
			}
			echo '</div>';
		}

	}

}
