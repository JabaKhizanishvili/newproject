<?php
/**
 * @version		$Id: calendar.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is included in WSCMS
defined( 'PATH_BASE' ) or die( 'Restricted access' );

/**
 * Renders a calendar element
 *
 * @package 	WSCMS.Framework
 * @subpackage	Parameter
 * @since		1.5
 */
class FilterElementRange extends FilterElement
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Calendar';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name, array() );
		$Start = C::_( 'start', $value );
		$End = C::_( 'end', $value );
		$Format = ( $node->attributes( 'format' ) ? $node->attributes( 'format' ) : '%Y-%m-%d' );
		$Class = $node->attributes( 'class' ) ? $node->attributes( 'class' ) : 'form-control';
		if ( empty( $value ) )
		{
			$value = $node->attributes( 'default' );
		}
		ob_start();
		?>
		<div class="row">
			<div class="col-sm-6">
				<?php echo $this->RenderCalendar( $Start, $Format, $Class, $id . '_start', $name . '[start]' ); ?>
			</div>
			<div class="col-sm-6">
				<?php echo $this->RenderCalendar( $End, $Format, $Class, $id . '_end', $name . '[end]' ); ?>
			</div>
		</div>
		<?php
		$Content = ob_get_clean();
		return$Content;

	}

	private function RenderCalendar( $Date, $Format, $Class, $id, $name )
	{
		if ( !empty( $Date ) )
		{
			$Date = new PDate( $Date );
			$value = $Date->toFormat( $Format );
		}
		else
		{
			$value = '';
		}
		return HTML::_( 'calendar', $value, $name, $id, $Format, array( 'class' => $Class ) );

	}

}
