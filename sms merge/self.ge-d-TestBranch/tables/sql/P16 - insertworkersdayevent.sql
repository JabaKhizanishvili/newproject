CREATE OR REPLACE PROCEDURE insertworkersdayevent(p_date_start date,
                                                  p_date_end   date) IS
BEGIN
  FOR v_user IN (SELECT *
                   FROM hrs_workers t
                  WHERE --(t.category_id = 1) 
                 --
                 -- and 
                  (t.active > -1)) loop
    begin
      begin
        -- call the procedure 
        insertworkerdayevent(p_date_start, p_date_end, v_user.id);
      END;
    END;
  
  END LOOP;

END insertworkersdayevent;
