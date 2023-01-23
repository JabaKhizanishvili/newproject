<?PHP

class LanguageTable extends TableLib_languagesInterface
{
    public $_DATE_FIELDS = [
        'REC_DATE' => 'yyyy-mm-dd hh24:mi:ss',
        'CHANGE_DATE' => 'yyyy-mm-dd hh24:mi:ss',
    ];

    public function __construct()
    {
        parent::__construct('lib_languages', 'ID', 'sqs_lib_languages.nextval');
    }


    public function check()
    {
        $this->LIB_LANGUAGE = trim($this->LIB_LANGUAGE);
        $this->FROM_LANGUAGE = trim($this->FROM_LANGUAGE);
        $this->TARGET_LANGUAGE = trim($this->TARGET_LANGUAGE);
        

        return true;

    }

}
