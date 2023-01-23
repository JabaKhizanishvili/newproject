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
class JGridElementbreportlink extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'breportlink';

	public function fetchElement( $row, $node, $config )
	{
		$Year = C::_( 'data.year', $config, PDate::Get()->toFormat( '%Y' ) );
		if ( empty( $Year ) )
		{
			$Year = PDate::Get()->toFormat( '%Y' );
		}
		$Key = trim( $node->attributes( 'key' ) );
		$Worker = explode( ' ', C::_( $Key, $row ) );
		$StartDate = PDate::Get( 'first day of January ' . $Year )->toFormat( '%d-%m-%Y' );
		$EndDate = PDate::Get( '31.12.' . $Year )->toFormat( '%d-%m-%Y' );
		$URL = '?firstname=' . C::_( '0', $Worker ) . '&lastname=' . C::_( '1', $Worker ) . '&start_date=' . $StartDate . '&end_date=' . $EndDate . '&option=r_bulletins';
		ob_start();
		?>
		<a href="<?php echo $URL; ?>" class="btn btn-default" target="_blank">
			<?php echo Text::_( 'Report' ); ?>
		</a>
		<?php
		$Content = ob_get_clean();

		return $Content;

	}

}
