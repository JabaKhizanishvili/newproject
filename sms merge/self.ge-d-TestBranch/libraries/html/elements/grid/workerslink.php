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
class JGridElementWorkerslink extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'workerslink';

	public function fetchElement( $row, $node, $config )
	{
		$Key = trim( $node->attributes( 'key' ) );
		$ORG = (int) C::_( 'ORG', $row, 0 );
		$SCHEDULE = (int) C::_( $Key, $row, 0 );
		$DATE = trim( Request::getState( C::_( '_option', $config ), 'start_date', '' ) );
		$html = '';
		?>
		<a rel="iframe-<?php echo $ORG . $SCHEDULE; ?>" class="modal-frame btn btn-success" href="?option=person_orgs&layout=modal&org=<?php echo $ORG; ?>&date=<?php echo $DATE; ?>&staffschedule=<?php echo $SCHEDULE; ?>&tmpl=modal&iframe=true&height=97%&width=97%">
			<?php echo Text::_( 'View' ); ?>
		</a>
		<?php
		return $html;

	}

}
