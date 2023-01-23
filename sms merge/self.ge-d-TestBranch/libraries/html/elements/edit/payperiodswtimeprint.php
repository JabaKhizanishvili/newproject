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
class JElementPayPeriodsWTimeprint extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'PayPeriodsWTimeprint';

	public function fetchElement( $name, $value, $node, $control_name )
	{
        $worker = $this->_parent->get('WORKER');
        $Depts = $this->getAccuracyPeriod($worker);

		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Org Place' ) );
        foreach ( $Depts as $dept )
        {
            $val = $dept->ID;
            $start = $dept->P_START;
            $pname = $dept->LIB_TITLE;
            $end = $dept->P_END;
            $text = $pname.' / '. explode( ' ', $start )[0] . ' - ' . explode( ' ', $end )[0];
            $options[] = HTML::_( 'select.option', $val, $text );
        }
        echo '<input type="hidden" name="params[' . $name . ']" id="params' . $name . '" value="' . $value . '">';
        return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name );

	}

    public function getAccuracyPeriod( $worker )
    {
        $Query = 'select pp.ID, ap.LIB_TITLE, pp.P_START, pp.P_END '
            . ' from slf_worker sw '
            . ' left join LIB_F_SALARY_TYPES fs on fs.ID = sw.SALARYTYPE '
            . ' left join LIB_F_ACCURACY_PERIODS ap on ap.ID = fs.ACCURACY_PERIOD '
            . ' left join slf_pay_periods pp on pp.PID = ap.ID '
            . ' where sw.id = ' . $worker
            . ' and pp.STATUS = 0 '
            . ' order by pp.P_START asc ';
        return DB::LoadObjectList( $Query );

    }

}
