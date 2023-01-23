<?php

class gettimedata
{

    /**
     * 
     * @return type
     */
    public function GetService()
    {
        Request::setVar('format', 'json');
        ob_start();
        require 'tmpl.php';
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }

}
