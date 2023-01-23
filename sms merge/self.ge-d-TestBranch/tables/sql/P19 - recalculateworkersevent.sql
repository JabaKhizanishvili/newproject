CREATE OR REPLACE PROCEDURE recalculateworkersevent(p_date_start date,
                                                    p_date_end   date,
                                                    p_worker     number) IS
BEGIN
  DELETE FROM hrs_staff_events t
   WHERE t.staff_id = p_worker
     AND t.event_date BETWEEN p_date_start AND p_date_end;
  BEGIN
    FOR userdata IN (SELECT t.*
                       FROM hrs_transported_data t
                      WHERE t.user_id = p_worker
                        AND t.rec_date BETWEEN trunc(p_date_start) AND
                            to_date(to_char(p_date_end, 'yyyy-mm-dd') ||
                                    '23:59:59',
                                    'yyyy-mm-dd hh24:mi:ss')
                      ORDER BY t.rec_date ASC) LOOP
      /*pkg_hrs_monitoring.setworkersevent(userdata.user_id, userdata.door_type, userdata.rec_date);
      */
      dbms_output.put_line(userdata.rec_date);
    END LOOP;
  END;
END recalculateworkersevent;
