<?php

function Cdef($object, $name)
{
    static $_class = array();
    if(empty($name))
    {
        die('Class Name Incorrect');
    }
    if(isset($_class[$name]))
    {
        return;
    }
    $_class[$name] = 1;
    $File = dirname(__FILE__) . DS . $name . '.php';
    $FileContent = '<?php' . "\n";
    $FileContent .= 'class ' . $name . "\n";
    $FileContent .= '{ ' . "\n";
    if(is_object($object))
    {
        foreach($object as $k => $v)
        {
            if(empty($k))
            {
                continue;
            }
            $FileContent .="\t" . 'public $' . $k . ' = NULL;' . "\n";
        }
    }

    $FileContent .= '}' . "\n";
    file_put_contents($File, $FileContent);
}