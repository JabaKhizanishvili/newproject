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
class JGridElementGWorkers extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'GWorkers';

	public function fetchElement( $row, $node, $config )
	{
		$html = '';
		$translate = trim( $node->attributes( 't' ) );
		$Tscope = trim( $node->attributes( 'tscope' ) );
		$key = trim( $node->attributes( 'key' ) );
		if ( $key )
		{
			if ( isset( $row->ID ) )
			{
				$Workers = $this->getWorkers( $row->ID );
				$text = [];
				foreach ( $Workers as $worker )
				{
					$T = '';
					if ( $translate == 1 )
					{
						$T = XTranslate::_( $worker->FIRSTNAME, $Tscope );
						$T .= ' ' . XTranslate::_( $worker->LASTNAME, $Tscope );
					}
					else
					{
						$T = $worker->FIRSTNAME . ' ' . $worker->LASTNAME;
					}

					$text[] = $T;
				}
				$Text = implode( ', ', $text );
				$html = Helper::MakeToolTip( $Text, 50, 0 );
			}
		}
		return $html;

	}

	public function getWorkers( $Group )
	{
		static $WorkersData = array();
		if ( !isset( $WorkersData[$Group] ) )
		{
			$Query = 'select '
							. ' w.firstname, '
							. ' w.lastname '
							. ' from slf_persons w '
							. ' left join REL_WGROUPS wg on w.id = wg.worker '
							. ' where '
							. ' group_id = ' . (int) $Group
							. ' order by wg.ordering asc, w.firstname ';
			$Data = DB::LoadObjectList( $Query );
			$WorkersData[$Group] = $Data;
		}
		return $WorkersData[$Group];

	}

}
