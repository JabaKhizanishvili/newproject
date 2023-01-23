<?PHP

class attributesTable extends TableLib_attributesInterface
{
    public function __construct()
    {
        parent::__construct('lib_attributes', 'ID', 'sqs_attributes.nextval');
    }

    public function check()
    {
        $this->LIB_TITLE = trim($this->LIB_TITLE);
        $this->COLOR = trim($this->COLOR);
        $this->DESTINATION = (int)trim($this->DESTINATION);
        $this->SHOW_IN_PROFILE = (int)trim($this->SHOW_IN_PROFILE);

        if (empty($this->LIB_TITLE) || empty($this->COLOR) || $this->DESTINATION == -1 || !isset($this->SHOW_IN_PROFILE)) {
            return false;
        }

        return true;

    }

}
