<?PHP

class unitorgTable extends TableLib_unitorgsInterface
{

    public function __construct()
    {
        parent::__construct('lib_unitorgs', 'ID', 'library.nextval');
    }

    public function check()
    {
        $this->LIB_TITLE = trim($this->LIB_TITLE);
        $this->LIB_DESC = trim($this->LIB_DESC);
        $this->ACTIVE = (int) trim($this->ACTIVE);
        if(empty($this->LIB_TITLE))
        {
            return false;
        }
        return true;
    }

}
