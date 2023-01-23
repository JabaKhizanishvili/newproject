<?php

defined('PATH_BASE') or die('Restricted access');

class SModel extends Model
{

    public function __construct($params)
    {
        parent::__construct($params);
    }

    public function getItems()
    {
        return new stdClass();
    }

    public function SaveData($data)
    {
        $this->Table->Clear();
        foreach($data as $key => $value)
        {
            if(!$this->Table->bind(array('KEY' => $key, 'VALUE' => $value)))
            {
                return false;
            }
            if(!$this->Table->check())
            {
                return false;
            }
            if(!$this->Table->store())
            {
                return false;
            }
            $this->Table->reset();
        }
        return true;
    }

}
