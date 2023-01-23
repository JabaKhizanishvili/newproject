<?php
require_once PATH_BASE . DS . 'libraries' . DS . 'Units.php';

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
class JElementPayperiodswtype extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'payperiodswtype';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Periods = $this->getAccuracyPeriod();
		$limit = $node->attributes( 'limit' );

		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Org Place' ) );
		foreach ( $Periods as $period )
		{
			$val = $period->ID;
			if ( $limit == '1' )
			{
				$text = $period->LIB_TITLE;
			}
			else
			{
				$text = $period->TITLE;
			}
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name );

	}

    public function getAccuracyPeriod( $period_type = 0 )
    {
        $where = '';
        if ( $period_type > 0 )
        {
            $where = ' and p.pid = ' . (int) $period_type;
        }
        $Query = 'select p.*, ac.lib_title p_name from slf_pay_periods p '
            . ' left join LIB_F_ACCURACY_PERIODS ac on ac.PERIOD_TYPE = p.P_TYPE '
            . ' where '
            . ' p.status = 1 '
            . ' and ac.period_type = p.p_type'
            . $where
            . ' and p.status = 0 '
            . ' order by p.p_code asc ';
        return DB::LoadObjectList( $Query );

    }

}
