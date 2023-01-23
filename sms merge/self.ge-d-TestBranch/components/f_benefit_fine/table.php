<?php class f_benefit_fineTable extends Tablelib_f_salary_sheetsInterface
        {

        public function __construct()
        {
        parent::__construct('lib_f_salary_sheets', 'ID', 'sqs_f_salary_sheets.nextval');
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