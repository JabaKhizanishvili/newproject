create or replace procedure ReCalcAll(p_date_start varchar2,
                                      p_date_end   varchar2) is
begin

  FOR v_user IN (SELECT *
                   FROM hrs_workers_sch t
                    where (t.active > -1)) loop
    begin
      begin
        -- call the procedure 
        recalc(p_date_start, p_date_end, v_user.id);
      END;
    END;
  
  END LOOP;
end ReCalcAll;
