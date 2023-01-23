<?php

class HelpContent
{

    protected $key = null;
    protected $host = null;
    private $content = null;
    private $info = null;

    function __construct($key, $host)
    {
        $this->host = $host;
        $this->key = $key;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function setInfo($info)
    {
        $this->info = $info;
    }

    public function getHelpContent()
    {
        $this->_getHTML();
        if(empty($this->content))
        {
            return false;
        }
        $this->SetBase();
        $this->Clean();
        $this->SetResize();
        return true;
    }

    protected function _getHTML()
    {
        set_time_limit(0);
        $link = str_replace(' ', '%20', $this->host . '/' . $this->key . '.htm');
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //timeout in seconds
        $Content = trim(curl_exec($ch));
        $this->setInfo(curl_getinfo($ch));
        curl_close($ch);

        if($this->info['http_code'] != 200)
        {
            $Content = '<!DOCTYPE html>'
                    . '<html xmlns="http://www.w3.org/1999/xhtml">'
                    . '<head>'
                    . '<script type="text/javascript" src="jquery.js"></script>'
                    . '</head>'
                    . '<body>'
                    . 'Help Content Not found!'
                    . '<br /><br /><pre>'
                    . 'Page Key : ' . $this->key
                    . '</pre></body>'
                    . '</html>'
            ;
        }
        $this->setContent($Content);
    }

    private function SetBase()
    {
        $base = '<head>' . PHP_EOL
                . '<base href = "' . $this->host . '/" />' . PHP_EOL;
        $this->setContent(str_replace('<head>', $base, $this->getContent()));
    }

    private function SetResize()
    {
        $Code = '<style type = "text/css">img{max-width: 900px;
}</style>'
                . '<script type = "text/javascript">'
                . ' $(document).ready(function() {'
                . 'var body = document.body, html = document.documentElement;
'
                . 'var height = Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight) + 50;
'
                . '$("#frame' . $this->key . '", window.parent.document).css("height", height);
});</script>'
                . '</body>' . PHP_EOL;
        $this->setContent(str_replace('</body>', $Code, $this->getContent()));
    }

    private function Clean()
    {
        $this->setContent(str_replace('HMSyncTOC', '//HMSyncTOC', $this->getContent()));
        $this->setContent(str_replace('<a ', '<a target="_blank" ', $this->getContent()));
    }

}
