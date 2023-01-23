<?php
// Created by Irakli Gzirishvili 27-10-2021.

class FilterElementEmail extends FilterElement
{
	protected $_name = 'Email';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$class = ( $node->attributes( 'class' ) ? 'class="form-control ' . $node->attributes( 'class' ) . '"' : 'class="form-control"' );
		$html = '<input  type="email" ' . $class . ' value="' . $value . '" id = "' . $id . '" name = "' . $name . '" />';
		return $html;
	}

}