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
class JGridElementbilllink extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'billlink';

	public function fetchElement( $row, $node, $config )
	{
		$Key = trim( $node->attributes( 'key' ) );
		$ID = C::_( $Key, $row );
		$BILL_ID = C::_( 'BILL_ID', $row );
		$html = '';
		?>
		<a rel="iframe-<?php echo $ID; ?>" class="modal-frame btn btn-success" href="?option=bill&id=<?php echo $BILL_ID; ?>&user=<?php echo $ID; ?>&tmpl=modal&iframe=true&height=97%&width=97%">
			<?php echo Text::_( 'View' ); ?>
		</a>
		<?php
		return $html;

	}

}
