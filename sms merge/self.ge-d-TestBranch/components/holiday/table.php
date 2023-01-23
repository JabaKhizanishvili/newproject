<?PHP

class HolidayTable extends TableLib_holidaysInterface
{

    public function __construct()
    {
        parent::__construct('lib_holidays', 'ID', 'library.nextval');
    }

    public function check()
    {
        $this->LIB_TITLE = trim($this->LIB_TITLE);
        $this->LIB_MONTH = trim($this->LIB_MONTH);
        $this->LIB_DAY = trim($this->LIB_DAY);
        $this->ACTIVE = (int) trim($this->ACTIVE);
        if(empty($this->LIB_TITLE))
        {
            return false;
        }
        if(empty($this->LIB_MONTH))
        {
            return false;
        }
        if(empty($this->LIB_DAY))
        {
            return false;
        }
        return true;
    }

}
