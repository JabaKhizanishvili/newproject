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
class JGridElementPrintEXP extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'PrintEXP';

	public function fetchElement( $row, $node, $group )
	{
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$LimitType = trim( $node->attributes( 'limit_type' ) );
		$Length = intval( $node->attributes( 'length' ) );
		$Text = '';
		$ID = Mail::generateRandomString( 10 );
		if ( isset( $row->{$key} ) )
		{
			$TextM = trim( stripslashes( $row->{$key} ) );
			switch ( $LimitType )
			{
				case 1:
					$Text = Helper::MakeToolTip( $TextM, $Length, 0 );
					break;
				case 2:
					$Text = Helper::MakeToolTip( $TextM, $Length, 1 );
					break;
			}
			ob_start();
			?>
			<div class="tips" style="cursor: pointer;" id="first-<?php echo $ID; ?>" onclick="$(this).addClass('hidden');$('#second-<?php echo $ID; ?>').removeClass('hidden');">
				<?php echo $Text; ?>
			</div>
			<div class="tips hidden" id="second-<?php echo $ID; ?>" >
				<?php echo nl2br( $TextM ); ?>
			</div>
			<?php
			$Cpntent = ob_get_clean();
			return $Cpntent;
		}

	}

}
