create or replace procedure ReCalc(p_date_start varchar2,
                                   p_date_end   varchar2,
                                   p_worker     number) is
begin
  workereventsrecalculation(to_date(p_date_start, 'yyyy-mm-dd hh24:mi:ss'),
                            to_date(p_date_end, 'yyyy-mm-dd hh24:mi:ss'),
                            p_worker);
end ReCalc;
