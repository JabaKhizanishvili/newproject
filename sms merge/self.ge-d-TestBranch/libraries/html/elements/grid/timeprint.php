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
class JGridElementTimePrint extends JGridElement
{
    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'numberprint';

    public function fetchElement( $row, $node, $group )
    {
        /* @var $node SimpleXMLElements */
        $key = trim( $node->attributes( 'key' ) );
        $decN = $node->attributes( 'dec' );
        $Text = '';
        if ( isset( $row->{$key} ) )
        {
            $val = trim( stripslashes( $row->{$key} ) );
            $Text = Xhelp::strNumber( $val, $decN );

            $DecimalToHrsMin = $this->DecimalToHrsMin((float)$Text );
            $Text = (!Helper::getConfig('OVERTIMEWORKERS_DECIMAL_TO_HRS_MIN'))?$Text:($DecimalToHrsMin["Hrs"])."სთ : ".($DecimalToHrsMin["Min"])."წთ";

        }
        return $Text;

    }
    public static function DecimalToHrsMin($decimal){
        $Hrs = floor(abs($decimal));
        if(!((int)$decimal==$decimal)) {
            $min = explode('.', $decimal);
            $min = (float)('0.' . $min[1]) * 60;
            $val1 = explode('.', $min);
            if(!((int)$min==$min)) {
                if ($val1[1][0] >= 5) {
                    $min = ceil($min);
                } else {
                    $min = floor($min);
                }
            }else{
                $min =$min;
            }
        }else{
            $min = 0;
        }
        return ["Hrs"=>$Hrs,"Min"=>$min];
    }
}
