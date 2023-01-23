<?php

require_once (PATH_BASE . DS . 'gateway' . DS . 'sms_send.php');

class SMSHelper
{

    public static function GenSMS($d)
    {
        $sms = '';
        if(isset($d->SMS))
        {
            $sms = stripslashes($d->SMS);
        }
        else
        {
            return false;
        }
        foreach($d as $k => $v)
        {
            if($k == 'SMS')
            {
                continue;
            }
            $v = trim($v);
            $patern = '{' . $k . '}';
            if(is_numeric($v))
            {
                $v = abs($v);
            }
            $sms = str_replace($patern, $v, $sms);
        }
        //     $sms_at = str_replace("@", "%C2%A1", $sms); // @
        //    $sms_d = str_replace("$", "%C2%A4", $sms_at); // $
        return $sms;
    }

    public static function SendSMS($d)
    {
        $sms = trim(self::GenSMS($d));
        if(!empty($sms) && self::CheckNumber($d->PHONE))
        {

            $From = 'MAGTIDEBT';
            $SendSms = new SendSms();
            if($SendSms->Send($From, $d->PHONE, $sms, $d->ID))
            {
                if(!empty($d->ACCOUNT_ID))
                {
                    $procedure = 'val.debt_account.CreateAction';
                    $action = Helper::getConfig('sms_action');
                    $status = Helper::getConfig('sms_status');
                    $category = Helper::getConfig('sms_new_category');
                    $userID = Users::GetUserID();
                    $params = array(
                        ':p_account_id' => $d->ACCOUNT_ID,
                        ':p_comment' => $sms,
                        ':p_status' => $status,
                        ':p_category' => $category,
                        ':p_balance' => $d->MONET_BALANCE,
                        ':p_action_id' => $action,
                        ':p_user_id' => $userID,
                        ':p_update_account' => 1
                    );
                    DB::callProcedure($procedure, $params);
                }
                self::SMSSendLog($d->ID);
                $query = ' insert into val.sms_history '
                        . ' (id, account_id, phone, sms, debt_phone, sent_date,valid_date, delivery, template_id) '
                        . ' values ( '
                        . $d->ID . ', '
                        . DB::Quote($d->ACCOUNT_ID) . ', '
                        . DB::Quote($d->PHONE) . ', '
                        . DB::Quote($sms) . ','
                        . DB::Quote($d->DEBT_PHONE) . ', '
                        . ' sysdate ,'
                        . 'to_date(\'' . $d->VALID_DATE . '\', \'dd.mm.yyyy hh24:mi:ss\'), '
                        . ' 0, '
                        . DB::Quote($d->TEMPLATE_ID) . ' '
                        . ' ) ';
                if(DB::Query($query))
                {
                    $dSql = 'delete from  val.sms_numbers t where t.id = ' . $d->ID;
                    DB::Query($dSql);
                }
            }
        }
    }

    public static function CheckNumber($number)
    {
        $sql = 'select count(t.id) '
                . ' from sms_history t '
                . ' where t.phone= \'' . $number . '\' '
                . ' and t.delivery = 0 '
        ;
        $rows = (int) DB::LoadResult($sql);
        if($rows > 0)
        {
            return false;
        }
        return true;
    }

    public static function CheckNumberFormat($number)
    {
        $sql = 'select count(t.id) '
                . ' from sms_history t '
                . ' where t.phone= \'' . $number . '\' '
                . ' and t.delivery = 0 '
        ;
        $rows = (int) DB::LoadResult($sql);
        if($rows > 0)
        {
            return false;
        }
        return true;
    }

    public static function SMSSendLog($id)
    {
        $id = (int) $id;
        $sql = 'insert into sms_history_log '
                . ' (sms_id, send_date) '
                . ' values('
                . $id
                . ','
                . '  sysdate) '
        ;
        return DB::Query($sql);
    }

    public static function GoodNumber($val)
    {
        if(strlen($val) == 9)
        {
            return true;
        }
        return false;
    }

    public static function ReSendSMS($d)
    {
        $sms = trim($d->SMS);
        if(!empty($sms) && $d->PHONE)
        {
            $From = 'MAGTIDEBT';
            $SendSms = new SendSms();
            if($SendSms->Send($From, $d->PHONE, $sms, $d->ID))
            {
                self::SMSSendLog($d->ID);
                $procedure = ' val.debt_common.LogAction';
                $params = array(
                    ':v_log_action' => 'Resend SMS',
                    ':v_log_desc' => 'Resend SMS to Number: ' . $d->PHONE . ', with Text: ' . $d->SMS
                );
                DB::callProcedure($procedure, $params);
            }
        }
    }

}
