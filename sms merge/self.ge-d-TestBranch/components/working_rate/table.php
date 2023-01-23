<?PHP

class Working_rateTable extends TableLib_working_ratesInterface
{

    public function __construct()
    {
        parent::__construct('lib_working_rates', 'ID', 'library.nextval');
    }

    public function check()
    {
        $this->LIB_TITLE = trim($this->LIB_TITLE);
        $this->LIB_DESC = trim($this->LIB_DESC);
        $this->ACTIVE = (int) trim($this->ACTIVE);
				
        if(!is_numeric($this->WORK_DURATION))
        {
            return false;
        }
        if(empty($this->LIB_TITLE))
        {
            return false;
        }
        if(empty($this->WORK_DURATION) || $this->WORK_DURATION < 1 || $this->WORK_DURATION > 100)
        {
            return false;
        }
        return true;
    }

}
