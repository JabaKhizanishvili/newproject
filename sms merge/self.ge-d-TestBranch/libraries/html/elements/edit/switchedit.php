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
class JElementSwitchEdit extends JElement
{
    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'Switchedit';

    public function fetchElement( $name, $valueIN, $node, $control_name )
    {
        $value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );

        $key = trim( $node->attributes( 'key' ) );
        $Text = '';
        $Start = '';
        $End = '';

        foreach ( $node->children() as $option )
        {
            $val = $option->attributes( 'value' );

            if ( $value == $val )
            {
                $Text = $Start . Text::_( $option->data() ) . $End;
            }

        }

        return '<div class="form-control form_field"><strong>' . $Text . '</strong></div>';
    }
    
    

}
