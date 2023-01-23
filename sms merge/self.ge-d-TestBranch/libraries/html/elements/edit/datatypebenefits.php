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
class JElementDatatypebenefits extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'datatypebenefits';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Collect = $this->Collect();
		$IN = [];
		if ( !empty( $value ) )
		{
			if ( is_array( $value ) )
			{
				$value = implode( '|', $value );
			}

			$IN = (array) json_decode( $value );
		}

		$html = '<div class="checksBox">';
		foreach ( $Collect as $Key => $Data )
		{
			$data = C::_( 'data', $Data, [] );
			$type = C::_( 'type', $Data );
			$ch1 = ' checked ';
			if ( !in_array( $Key, array_keys( $IN ) ) )
			{
				$ch1 = '';
			}

			$html .= '<div class="level_0 itemrow_' . $Key . ' radio radioparent" data-rel="' . $Key . '"><input type="checkbox" ' . $ch1 . ' class="self-border" name="params[' . $name . '][' . $Key . ']" id="params' . $name . '_' . $Key . '" value="' . $Key . '"><label for="params' . $name . '_' . $Key . '">' . $type . '</label></div><div class="cls"></div>';
			foreach ( $data as $category => $each )
			{
				$category_data = $this->getBenefitCategories( $category );
				$category_id = $category_data->ID;
				$category_name = $category_data->LIB_TITLE;

				$ch2 = '';

				$html .= '<div  style="display:none;"  class="level_1 itemrow_' . $Key . ' itemrow_' . $Key . $category_id . ' radio radioparent" data-rel="' . $Key . $category_id . '"><input type="checkbox" ' . $ch2 . ' class="self-border" name="params[' . $name . '][' . $Key . '][' . $category_id . ']" id="params' . $name . '_' . $Key . $category_id . '" value="' . $category_id . '"><label for="params' . $name . '_' . $Key . $category_id . '">' . $category_name . '</label><span class="bi showLevels bi-chevron-down"></span></div><div class="cls"></div>';
				foreach ( $each as $bid => $bname )
				{
					$arr = (array) explode( '|', C::_( $Key, $IN ) );
					$ch3 = ' checked ';
					if ( !in_array( $bid, $arr ) )
					{
						$ch3 = '';
					}

					$html .= '<div style="padding-left: 60px; display:none;" class="level_3 sub_' . $Key . ' itemrow_' . $Key . ' itemrow_' . $Key . $category_id . ' radio"><input dep="' . $Key . $category_id . '" type="checkbox" ' . $ch3 . ' class="self-border" name="params[' . $name . '][' . $Key . '][' . $category_id . '][]" id="params' . $name . '_' . $Key . '_' . $bid . '" value="' . $bid . '"><label for="params' . $name . '_' . $Key . '_' . $bid . '"> ' . $bname . '</label></div><div class="cls"></div>';
				}
			}
		}
		$html .= '</div>';
		Helper::SetJS( '$(\'.level_3 input\').each(function(){
	if($(this).is(":checked"))
	{
		var set = $(this).attr(\'dep\');
		console.log(set);
    $(\'.itemrow_\' + set + \' input\').prop(\'checked\', true);
  }
});' );
		return $html;

	}

	public function Collect()
	{
		$Tables = [];

//		Salary
		$Tables[0]['type'] = Text::_( 'collected salary' );

//		Regular benefits
		$Tables[1]['type'] = Text::_( 'regular_benefit' );
		foreach ( $this->getBenefitTypes( 1 ) as $id => $data )
		{
			foreach ( $data as $bid => $bdata )
			{
				$Tables[1]['data'][$id][$bid] = $bdata->LIB_TITLE;
			}
		}

//		Iregular benefits
		$Tables[2]['type'] = Text::_( 'iregular_benefit' );
		foreach ( $this->getBenefitTypes( 2 ) as $id => $data )
		{
			foreach ( $data as $bid => $bdata )
			{
				$Tables[2]['data'][$id][$bid] = $bdata->LIB_TITLE;
			}
		}

		return $Tables;

	}

	public function getBenefitTypes( $regularity = -1 )
	{
		$Query = 'select '
						. ' p.id, '
						. ' p.lib_title, '
						. ' p.benefit category '
						. ' from LIB_F_BENEFIT_TYPES p '
						. ' where '
						. ' p.active > 0 '
						. ($regularity > 0 ? ' and p.regularity in (3, ' . $regularity . ')' : '')
						. ' order by p.lib_title asc';
		$result = DB::LoadObjectList( $Query );

		$collect = [];
		foreach ( $result as $val )
		{
			$collect[$val->CATEGORY][$val->ID] = $val;
		}
		return $collect;

	}

	public function getBenefitCategories( $id = 0 )
	{
		static $getBenefitCategories = null;
		if ( is_null( $getBenefitCategories ) )
		{
			$Query = 'select '
							. ' c.id, '
							. ' c.lib_title '
							. ' from lib_f_benefits c '
							. ' where '
							. ' c.active > 0 ';
			$getBenefitCategories = DB::LoadObjectList( $Query, 'ID' );
		}

		if ( $id > 0 )
		{
			return C::_( $id, $getBenefitCategories );
		}

		return $getBenefitCategories;

	}

}
