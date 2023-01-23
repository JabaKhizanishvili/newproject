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
class JGridElementComment extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Comment';

	public function fetchElement( $row, $node, $config )
	{

		$html = '';
		$key = trim( $node->attributes( 'key' ) );
		$FLOW = C::_( $key, $row, 0 );
//		$LibFlowID = $this->getFlowID( $FLOW );
		$html .= ' <span class="commentTip" id="flow_' . $FLOW . '" >';
		$html .= '<img src="templates/images/comment.png" alt="" />';
		$html .= ' </span>';
		return $html;

	}

//	public function getFlowID( $FLOW )
//	{
//		$Query = ' select wf.FLOW from CWS_WORKFLOWS wf where wf.ID = ' . DB::Quote( $FLOW );
//		return DB::LoadResult( $Query );
//
//	}
}
