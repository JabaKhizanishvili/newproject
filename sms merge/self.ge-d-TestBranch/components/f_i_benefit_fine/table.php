<?php class f_i_benefit_fineTable extends Tableslf_worker_benefitsInterface
{

    public function __construct()
    {
        parent::__construct('slf_worker_benefits', 'ID', 'sqs_worker_benefits.nextval');
    }

    public function check()
    {
        $this->WORKER = trim( $this->WORKER );
        $this->BENEFIT_ID = trim( $this->BENEFIT_ID );
        $this->COST = trim( $this->COST );
        $this->PERIOD_ID = trim( $this->PERIOD_ID );
        $this->WORKER_SHARE = trim ( $this->WORKER_SHARE);
        $this->COMPANY_SHARE = trim ( $this->COMPANY_SHARE);
        $this->TYPE = trim ( $this->TYPE );
        if ( empty( $this->PERIOD_ID ) )
        {
            return false;
        }
        if ( empty( $this->WORKER ) )
        {
            return false;
        }
        if ( empty( $this->BENEFIT_ID ) )
        {
            return false;
        }
        if ( empty( $this->COST ) )
        {
            return false;
        }

        return true;
    }

}