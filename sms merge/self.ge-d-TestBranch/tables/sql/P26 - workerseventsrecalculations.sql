CREATE OR REPLACE PROCEDURE workerseventsrecalculations(p_date_start date,
                                                        p_date_end   date) IS
BEGIN
  FOR v_user IN (SELECT *
                   FROM hrs_workers t
                 
                  WHERE --(t.category_id = 1) 
                 -- and
                  (t.active > -1)
                 -- and
                 --    p.uidx is null
                 --and rownum < 30 
                 ) loop
    begin
      begin
        -- call the procedure 
        workereventsrecalculation(p_date_start, p_date_end, v_user.id);
        -- INSERT INTO tmp_user_proc (uidx) VALUES (v_user.id);
        dbms_output.put_line(v_user.id || ' ' || v_user.firstname || ' ' ||
                             v_user.lastname);
        COMMIT;
      END;
    END;
  END LOOP;
END workerseventsrecalculations;
