<?php
/**
 * @version		$Id: sql.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a SQL element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class FilterElementgraphtimes extends FilterElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'graphtimes';

	public function fetchElement( $name, $id, $node, $config )
	{
		$class = '';
		$value = $this->GetConfigValue( $config['data'], $name );
		$data = $this->GetGraphTimes();
		$options = array();
		$options[] = HTML::_( 'select.option', -1, Text::_( 'Select Graph Time' ) );
		foreach ( $data as $Item )
		{
			$options[] = HTML::_( 'select.option', C::_( 'ID', $Item ), C::_( 'LIB_TITLE', $Item ) );
		}
		return HTML::_( 'select.genericlist', $options, $name, 'class="form-control' . $class . '" ', 'value', 'text', $value, $id );

	}

	public function GetGraphTimes()
	{
		$query = 'select *
  from (select t.id, max(t.lib_title) lib_title
          from LIB_GRAPH_TIMES t
          left join REL_TIME_GROUP tg
            on tg.time_id = t.id
         where tg.group_id in (select wg.group_id
                                 from rel_worker_chief wc
                                 left join rel_workers_groups wg
                                   on wg.worker = wc.worker
                                where wc.chief in (select m.id from hrs_workers m where m.PARENT_ID =   ' . DB::Quote( Users::GetUserID() ) . ' ))
           and t.active > -1
           AND t.owner = 1
         group by t.id) k
 order by k.lib_title asc';
		$data = DB::LoadObjectList( $query, 'ID' );
		return $data;

	}

}
