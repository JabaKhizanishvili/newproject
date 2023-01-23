<?PHP

class RolesTable extends TableLib_rolesInterface
{

    public function __construct()
    {
        $this->ORDERING = 999;
        parent::__construct('lib_roles', 'ID', 'library.nextval');
    }

    public function check()
    {
        $this->LIB_TITLE = trim($this->LIB_TITLE);
        $this->LIB_DESC = trim($this->LIB_DESC);
        $this->ACTIVE = (int) trim($this->ACTIVE);
        $this->LIB_REL_MENUS =  trim($this->LIB_REL_MENUS);
        if(empty($this->LIB_REL_MENUS))
        {
            return false;
        }
        if(empty($this->LIB_TITLE))
        {
            return false;
        }
        return true;
    }

}
