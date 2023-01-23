<?php

class JElementWorker extends JElement
{

    var $_name = 'Worker';

    public function fetchElement($name, $value, $node, $control_name)
    {
        $link = '?';
        $width = $node->attributes('width', '95%');
        $height = $node->attributes('height', '95%');
        $uri = URI::getInstance($link);
        $uri->setVar('option', 'workersmodal');
        $uri->setVar('tmpl', 'modal');
        $uri->setVar('groups', '0');
        $uri->setVar('js', 'getWorker');
        $uri->setVar('width', $width);
        $uri->setVar('iframe', 'true');
        $uri->setVar('height', $height);
        $id = substr(md5(rand(0, 999999999) . microtime(1)), rand(1, 20), 7);
        $return = '<div class="WorkersBlock">
        <div class="WorkerContainer"></div>
        <div class="cls"></div>
        <div class="WorkersButtons">'
                . '<a class="btn btn-primary" rel="iframe-' . $id . '" href="' . $uri->toString() . '">'
                . Text::_('Select')
                . '</a>
            <div class="cls"></div>
            <input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="" class="WorkerData" />
        </div>
    </div>';
        $JS = '$("a[rel^=\'iframe-' . $id . '\']").prettyPhoto();';
        $JS .= 'var WorkerData = "' . $value . '";'
                . 'if(WorkerData!="")'
                . '{'
                . 'getWorker(WorkerData);'
                . '}';
        Helper::SetJS($JS);
        return $return;
    }

}
