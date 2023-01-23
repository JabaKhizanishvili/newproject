<?php

/**
 * @version        $Id: list.php 1 2011-07-13 05:09:23Z $
 * @package    WSCMS.Framework
 * @copyright    Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license        GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a list element
 *
 * @package    WSCMS.Framework
 * @subpackage        Parameter
 * @since        1.5
 */
class JElementEntertime extends JElement
{
    /**
     * Element type
     *
     * @access    protected
     * @var        string
     */
    protected $_name = 'Entertime';

    public function fetchElement($name, $valueIN, $node, $control_name)
    {

        $size = ($node->attributes('size') ? 'size="' . $node->attributes('size') . '"' : '');
        $class = ($node->attributes('class') ? 'class="text_area ' . $node->attributes('class') . ' form-control  m-5 "' : 'class="form-control"');
        $Min = ($node->attributes('min') ? 'min="' . $node->attributes('min') . '"' : '');
        $Max = ($node->attributes('max') ? 'max="' . $node->attributes('max') . '"' : '');

        $MaxHours = ($node->attributes('maxhours') ?  $node->attributes('maxhours')  : '');
        $MinHours = ($node->attributes('minhours') ?  $node->attributes('minhours')  : '');

        $MaxMinute = ($node->attributes('maxminute') ?  $node->attributes('maxminute') : '');
        $MinMinute = ($node->attributes('minminute') ?  $node->attributes('minminute') : '');

        $Step = ($node->attributes('step') ? 'step="' . $node->attributes('step') . '"' : '');
        $StepForHrsMin = ($node->attributes('stepforhrsmin') ? 'step="' . $node->attributes('stepforhrsmin') . '"' : '');

//        $HourName = ($node->attributes('hoursname') ?  $node->attributes('hoursname')  : '');
//        $MinuteName =   ($node->attributes('minutename') ?  $node->attributes('minutename')  : '');
        $name = ($node->attributes('name') ?  $node->attributes('name')  : '');


        $value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
        $decN = $node->attributes( 'dec' );




        if ( $decN )
        {
            $value = Xhelp::strNumber( $value, $decN );
        }

        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */

        $js = <<<JS
            function setHrsMinDecimal(elH,elM){
                // Hours
                if(elH && elH.value>$MaxHours){
                    elH.value=elH.value[0];
                }
                if(elH && elH.value<$MinHours){
                    elH.value="";
                }
                // Minute
                if(elM && elM.value>$MaxMinute){
                    elM.value=elM.value[0];
                }
                if(elM && elM.value<$MinMinute){
                     elM.value="";
                }
 
                let Res = HrsMinToDecimal($("#paramsHOUR").val()+':'+$("#paramsMINUTE").val());
                $("#paramsDAY_COUNT").val(Res);
            }
          

JS;
        Helper::SetJS($js, false);
        $el = "";
        if(!Helper::getConfig('OVERTIMEWORKERS_DECIMAL_TO_HRS_MIN'))
        {
            $el = '<input value="' . ((float)$value) . '"  type="number"  name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '"   '. $class . ' ' . $size .$Min .$Max.$Step .' />';
        }
        else{
            $DecimalToHrsMin = $this->DecimalToHrsMin($value );
            $HourInput = '<input  value="'.$DecimalToHrsMin["Hrs"].'" id="paramsHOUR"  style="width:49%; float:left;" onkeyup="setHrsMinDecimal(this,null);  "  type="number"  placeholder="საათი"  ' . $class . '   ' . $size . $StepForHrsMin . ' />';
            $MinInput  = '<input   value="'.$DecimalToHrsMin["Min"].'" id="paramsMINUTE" style="width:49%; " onkeyup="setHrsMinDecimal(null,this);  "  type="number" placeholder="წუთი"  ' . $class . ' ' . $size . $StepForHrsMin . ' />';
            $hiden = '<input type="hidden"  name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '"  value="'.$value.'"  '. $class . ' ' . $size .$Min .$Max.$Step .' />';
            $el = $HourInput.$MinInput.$hiden;
        }
        return
            '<div class="d-flex justify-content-start">'.
            $el
            . '</div>';
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
