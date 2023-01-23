<?php

class JElementGroupWorkers extends JElement
{
	var $_name = 'GroupWorkers';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$link = '?';
		$width = $node->attributes( 'width', '95%' );
		$height = $node->attributes( 'height', '95%' );
		$uri = URI::getInstance( $link );
		$uri->setVar( 'option', 'workersmodal' );
		$uri->setVar( 'groups', '1' );
		$uri->setVar( 'option', 'workersmodal' );
		$uri->setVar( 'org', '_ORG_' );
		$uri->setVar( 'tmpl', 'modal' );
		$uri->setVar( 'js', 'getGroupWorkers' );
		$uri->setVar( 'width', $width );
		$uri->setVar( 'iframe', 'true' );
		$uri->setVar( 'height', $height );
		$id = substr( md5( rand( 0, 999999999 ) . microtime( 1 ) ), rand( 1, 20 ), 7 );
		$return = '<div class="WorkersBlock">
        <div class="WorkersContainer"></div>
        <div class="cls"></div>
        <div class="WorkersButtons">'
						. '<a class="btn btn-primary" rel="iframe-' . $id . '" id="workersmodal" href="' . $uri->toString() . '">'
						. Text::_( 'Add' )
						. '</a>
            <div class="cls"></div>
            <input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . $value . '" class="WorkersData" />
        </div>
    </div>';
		$JSG = '  var $ORGURL = "' . $uri->toString() . '";';
		$JS = 'SetOrg("workersmodal");'
						. ' $("#paramsORG").change(function () { SetOrg("workersmodal"); });'
						. ' $("a[rel^=\'iframe-' . $id . '\']").prettyPhoto();';
		$JS .= 'var WorkersData = "' . $value . '";'
						. 'if(WorkersData!="")'
						. '{'
						. 'getGroupWorkers(WorkersData);'
						. '}';
		Helper::SetJS( $JS );
		Helper::SetJS( $JSG, false );
		return $return;

	}

}
