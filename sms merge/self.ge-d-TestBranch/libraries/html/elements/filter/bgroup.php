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
class FilterElementBGroup extends FilterElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'BGroup';

	public function fetchElement( $name, $id, $node, $config )
	{
		$class = '';
		$value = $this->GetConfigValue( $config['data'], $name );
		$data = self::getWorkerGroups();
		$options = array();
		$options[] = HTML::_( 'select.option', -1, Text::_( 'Select Group' ) );
		foreach ( $data as $Item )
		{
			$IDx = explode( ',', C::_( 'IDX', $Item ) );
			$IDxF = array_flip( $IDx );
			$ID = null;
			if ( isset( $IDxF[$value] ) )
			{
				$ID = $value;
			}
			else
			{
				$ID = C::_( '0', $IDx );
			}
			$options[] = HTML::_( 'select.option', $ID, C::_( 'LIB_TITLE', $Item ) );
		}
		return HTML::_( 'select.genericlist', $options, $name, 'class="form-control' . $class . '" ', 'value', 'text', $value, $id );

	}

	public static function getWorkerGroups()
	{
		$Query = 'select 
 listagg(wg.lib_title, \',\') WITHIN GROUP(order by wg.lib_title) LIB_TITLE,
 listagg(m.group_id, \',\') WITHIN GROUP(order by m.group_id) idx
  from (select g.group_id,
               listagg(g.time_id, \',\') WITHIN GROUP(order by g.time_id) times
          from rel_time_group g
         group by g.group_id) m
 right join lib_workers_groups wg
    on wg.id = m.group_id
 where m.times is not null
   and m.group_id in
       (select wg.group_id
                        from rel_worker_chief wc
                        left join rel_workers_groups wg
                          on wg.worker = wc.worker
                       where wc.chief in (select m.id from hrs_workers m where m.PARENT_ID =  ' . DB::Quote( Users::GetUserID() )
						. ' ))
 group by m.times';
		return DB::LoadObjectList( $Query, 'ID' );

	}

}
