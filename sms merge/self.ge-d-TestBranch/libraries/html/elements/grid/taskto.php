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
class JGridElementTaskto extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Taskto';

	public function fetchElement( $row, $node, $config )
	{
		$Key = trim( $node->attributes( 'key' ) );
		$action = trim( $node->attributes( 'action' ) );
		$ID = C::_( $Key, $row, 0 );
		$html = '';
		?>
		<a class="btn btn-success" href="?option=controller&value=<?php echo $ID; ?>&task=<?php echo $action; ?>">
			<?php echo Text::_( $action ); ?>
		</a>
		<?php
		return $html;

	}

}
