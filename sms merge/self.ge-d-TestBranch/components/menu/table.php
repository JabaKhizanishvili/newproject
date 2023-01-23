<?PHP

class MenusTable extends TableLib_menusInterface
{

    public function __construct()
    {
        $this->ORDERING = 999;
        parent::__construct('lib_menus', 'ID', 'library.nextval');
    }

    public function check()
    {
        $this->LIB_TITLE = trim($this->LIB_TITLE);
        $this->ACTIVE = (int) trim($this->ACTIVE);
        $this->LIB_PARENT = (int) trim($this->LIB_PARENT);
        $this->LIB_LEVEL = (int) trim($this->LIB_LEVEL);
        if(empty($this->LIB_TITLE))
        {
            return false;
        }
        $TableParent = clone ($this);
        $TableParent->reset();
        if($this->LIB_PARENT)
        {
            /* @var $TableParent MenusTable */
            $TableParent->reset();
            $TableParent->load($this->LIB_PARENT);
            if(C::_('ID', $TableParent))
            {
                $this->LIB_LEVEL = C::_('LIB_LEVEL', $TableParent) + 1;
            }
        }
        return true;
    }

}
