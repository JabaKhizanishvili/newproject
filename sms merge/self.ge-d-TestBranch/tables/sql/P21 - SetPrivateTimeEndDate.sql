create or replace procedure SetPrivateTimeEndDate is
begin
  begin
    update HRS_APPLICATIONS t
       set t.end_date = t.end_date + (1 / 24 / 60 * 30)
     where t.type in (2)
       and t.start_date >= trunc(sysdate)
       and t.status > -1
       and t.end_date between sysdate + (1 / 24 / 60 * 5) and
           sysdate + (1 / 24 / 60 * 30);
    commit;
  end;

end SetPrivateTimeEndDate;
