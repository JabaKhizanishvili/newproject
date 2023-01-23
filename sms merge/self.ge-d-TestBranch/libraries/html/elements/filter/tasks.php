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
class FilterElementTasks extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'tasks';

	public function fetchElement( $name, $id, $node, $config )
	{
        $value = C::_($name, $config['data']);
        $Query = 'select '
            . ' t.id, '
            . ' t.lib_title '
            . ' from lib_wgroups t '
            . ' WHERE t.active =1 '
            . ' order by lib_title asc';
        $data = DB::LoadObjectList( $Query );

        $options = array();
        $options[] = HTML::_( 'select.option', '1', Text::_( 'Select Category' ) );
        $options[] = HTML::_( 'select.option', '0', Text::_( 'Object' ) );
        $options[] = HTML::_( 'select.option', '-1', Text::_( 'Object direct Chief' ) );
        $options[] = HTML::_( 'select.option', '-2', Text::_( 'curent User direct Chief' ) );
        $options[] = HTML::_( 'select.option', '-3', Text::_( 'REPLACER_WORKER' ) );
        $options[] = HTML::_( 'select.option', '-4', Text::_( 'Additional Chief' ) );
        $options[] = HTML::_( 'select.option', '-5', Text::_( 'curent User Additional Chief' ) );
//		$options[] = HTML::_( 'select.option', '-17', Text::_( 'Select from Chief Workers' ) );
//		$options[] = HTML::_( 'select.option', '-15', Text::_( 'initiator and From Task Attributes' ) );
//		$options[] = HTML::_( 'select.option', '-12', Text::_( 'Workers From Task Attributes' ) );
//		$options[] = HTML::_( 'select.option', '-11', Text::_( 'FROM TASK GROUP ATTRIBUTES' ) );
//		$options[] = HTML::_( 'select.option', '-9', Text::_( 'From Task Attributes' ) );
//		$options[] = HTML::_( 'select.option', '-8', Text::_( 'Task Attributes Workers' ) );
//		$options[] = HTML::_( 'select.option', '-7', Text::_( 'Select From Group' ) );
//		$options[] = HTML::_( 'select.option', '-5', Text::_( 'current User' ) );
//		$options[] = HTML::_( 'select.option', '-3', Text::_( 'From Previus Task' ) );
//		$options[] = HTML::_( 'select.option', '-2', Text::_( 'dinamic worker with attribute value' ) );
//		$options[] = HTML::_( 'select.option', '-1', Text::_( 'dinamic worker' ) );
//		$options[] = HTML::_( 'select.option', '0', Text::_( 'initiator' ) );
        if ( empty( $value )  )
        {
            $value = 0;
        }
        foreach ( $data as $dat )
        {
            $val = $dat->ID;
            $text = $dat->LIB_TITLE;
            $options[] = HTML::_( 'select.option', $val, $text );
        }
        return HTML::_( 'select.genericlist', $options, $name, ' class="form-control" onchange="setFilter();" ', 'value', 'text', (int) $value, $id );
    }
}
