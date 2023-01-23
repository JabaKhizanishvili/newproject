CREATE OR REPLACE PROCEDURE eventsrecalculationbyrange(p_date_start date,
                                                       p_date_end   date,
                                                       p_worker     number) IS
  v_end_date date;
BEGIN
  v_end_date := to_date(to_char(p_date_end, 'yyyy-mm-dd') || '23:59:59',
                        'yyyy-mm-dd hh24:mi:ss');
  DELETE FROM hrs_staff_events t
   WHERE t.staff_id = p_worker
     AND t.event_date BETWEEN p_date_start AND v_end_date;
  FOR userdata IN (SELECT t.*
                     FROM hrs_transported_data t
                    WHERE t.user_id = p_worker
                      AND t.rec_date BETWEEN p_date_start AND
                          to_date(to_char(p_date_start, 'yyyy-mm-dd') ||
                                  '23:59:59',
                                  'yyyy-mm-dd hh24:mi:ss')
                    ORDER BY t.rec_date ASC) LOOP
    BEGIN
      dbms_output.put_line(to_char(userdata.rec_date, 'yyyy-mm-dd hh24:mi') ||
                           ' - ' || userdata.door_type);
      pkg_workers_monitoring.setworkersevent(userdata.user_id,
                                             userdata.door_type,
                                             userdata.rec_date);
    END;
  END LOOP;
  COMMIT;
END eventsrecalculationbyrange;
