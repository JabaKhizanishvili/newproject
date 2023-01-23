<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

//require_once 'table.php';

class overtimeworkerModel extends Model
{
    protected $Table = null;

    public function __construct( $params )
    {
        $this->Table = AppHelper::getTable();
        parent::__construct( $params );

    }

    public function getItem()
    {
        $id = Request::getVar( 'nid', array() );
        if ( isset( $id[0] ) && !empty( $id[0] ) )
        {
            $this->Table->load( $id[0] );
        }
        if ( $this->Table->ID )
        {
            $StartDate = new PDate( $this->Table->START_DATE );
            $EndDate = new PDate( $this->Table->END_DATE );
            $this->Table->START_TIME = $StartDate->toFormat( '%H:%M' );
            $this->Table->END_TIME = $EndDate->toFormat( '%H:%M' );
        }

        return $this->Table;

    }

    public function SaveData( $data )
    {
        $Workers = (array) C::_( 'WORKER_ID', $data );

        $id = C::_( 'ID', $data );
        if ( $id )
        {
            $this->Table->load( $id );
        }

        if ( empty( $Workers ) )
        {
            XError::setError( 'Worker Incorrect!' );
            return false;
        }

        $PDate = trim( C::_( 'START_DATE', $data ) );
        $DAY_COUNT = number_format( C::_( 'DAY_COUNT', $data ), 2 );
        if ( !is_numeric( $DAY_COUNT ) || $DAY_COUNT <= 0 )
        {
            XError::setError( 'Overtime Hour Incorrect!' );
            return false;
        }
        if ( empty( $PDate ) )
        {
            XError::setError( 'Date Incorrect!' );
            return false;
        }
        $BaseDate = new PDate( C::_( 'START_DATE', $data ) );
        $StartDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 00:00:00' ) );
        $EndDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 23:59:59' ) );
        if ( $EndDate->toUnix() > PDate::Get()->toUnix() )
        {
            XError::setError( 'overtime Date Incorrect!' );
            return false;
        }
        if ( !$id )
        {
            $data['REC_USER'] = Users::GetUserID();
        }

        $count = 0;
        foreach ( $Workers as $id )
        {
            $O = XGraph::getWorkerDataSch( $id );
            $orgpid = C::_( 'ORGPID', $O );

            $data['TYPE'] = APP_OVERTIME;
            $data['WORKER'] = $orgpid;
            $data['WORKER_ID'] = $id;
            $data['START_DATE'] = $StartDate->toFormat();
            $data['END_DATE'] = $EndDate->toFormat();
            $data['DAY_COUNT'] = $DAY_COUNT;
            $data['INFO'] = trim( C::_( 'INFO', $data ) );

            if ( !$this->Table->bind( $data ) )
            {
                continue;
            }
            if ( !$this->Table->check() )
            {
                continue;
            }
            if ( !$this->Table->store() )
            {
                continue;
            }

            $count++;
        }

        if ( $count > 0 )
        {
            return true;
        }

        return false;

    }

    public function Delete( $data, $mode = 'archive' )
    {
        if ( is_array( $data ) )
        {
            foreach ( $data as $id )
            {
                $Date = new PDate();
                $this->Table->load( $id );
                if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
                {
                    if ( C::_( 'STATUS', $this->Table, 0 ) != 0 )
                    {
                        $link = '?option=' . $this->_option;
                        XError::setError( 'you cannot access task' );
                        Users::Redirect( $link );
                    }
                }
                $this->Table->STATUS = -2;
                $this->Table->DEL_USER = Users::GetUserID();
                $this->Table->DEL_DATE = $Date->toFormat( '%Y-%m-%d %H:%M:%S' );
                $this->Table->store();
            }
        }
        return true;

    }

    public function Approve()
    {
        $idx = Request::getVar( 'nid', array() );
        if ( is_array( $idx ) )
        {
            $date = new PDate();
            foreach ( $idx as $id )
            {
                $this->Table->load( $id );
                if ( C::_( 'ID', $this->Table ) )
                {
                    if ( C::_( 'STATUS', $this->Table, 0 ) != 0 )
                    {
                        $link = '?option=' . $this->_option;
                        XError::setError( 'Overtime Already Approved!' );
                        Users::Redirect( $link );
                    }
                    $this->Table->STATUS = 1;
                    $this->Table->APPROVE = Users::GetUserID();
                    $this->Table->APPROVE_DATE = $date->toFormat( '%Y-%m-%d %H:%M:%S' );
                    $this->Table->store();

                    $WorkerData = XGraph::GetOrgUser( C::_( 'WORKER', $this->Table ) );
                    $Subject = 'Your private overtime request confirmed.';
                    $TextLines = [];
                    $TextLines[] = C::_( 'FIRSTNAME', $WorkerData ) . ' ' . C::_( 'LASTNAME', $WorkerData );
                    $TextLines[] = 'თქვენი ზეგანაკვეთური დროის განაცხადი  დადასტურებულია';
                    $TextLines[] = 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $WorkerData );
                    $TextLines[] = 'თარიღი: ' . explode( ' ', C::_( 'START_DATE', $this->Table ) )[0];
                    $TextLines[] = 'საათი: ' . (float) number_format( C::_( 'DAY_COUNT', $this->Table ), 2 );

                    $Phone_Number = C::_( 'MOBILE_PHONE_NUMBER', $WorkerData );
                    Mail::sendAppSMS( $Phone_Number, $TextLines );
//					$Email = C::_( 'EMAIL', $WorkerData );
//					Mail::sendAppEMAIL( $Email, $Subject, $TextLines, $Worker );
                }
                else
                {
                    return false;
                }
            }
            return true;
        }

    }

}
