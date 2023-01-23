CREATE OR REPLACE PROCEDURE workereventsrecalculation(p_date_start date,
                                                      p_date_end   date,
                                                      p_worker     number) IS
  v_start_date   date;
  v_end_date     date;
  v_end_day      date;
  v_end_day_time date;
  v_Last_type    number := 0;
BEGIN
  v_start_date := p_date_start;
  v_end_date   := to_date(to_char(p_date_end, 'yyyy-mm-dd') || '23:59:59',
                          'yyyy-mm-dd hh24:mi:ss');

  DELETE FROM hrs_staff_events t
   WHERE t.staff_id = p_worker
     AND t.event_date BETWEEN v_start_date AND v_end_date;

  FOR v_day IN (SELECT (trunc(p_date_start) + LEVEL - 1) pdate
                  FROM dual
                CONNECT BY LEVEL <=
                           trunc(p_date_end) - trunc(p_date_start) + 1) LOOP
    BEGIN
      FOR userdata IN (SELECT t.*
                         FROM hrs_transported_data t
                        WHERE t.user_id = p_worker
                          AND t.rec_date BETWEEN v_day.pdate AND
                              to_date(to_char(v_day.pdate, 'yyyy-mm-dd') ||
                                      '23:59:59',
                                      'yyyy-mm-dd hh24:mi:ss')
                        ORDER BY t.rec_date ASC) LOOP
        BEGIN
          pkg_workers_monitoring.setworkersevent(userdata.user_id,
                                                 userdata.door_type,
                                                 userdata.rec_date,
                                                 userdata.time_id);
        END;
      END LOOP;
      begin
        v_end_day      := to_date(to_char(v_day.pdate + 1, 'yyyy-mm-dd') ||
                                  '07:00:00',
                                  'yyyy-mm-dd hh24:mi:ss');
        v_end_day_time := v_end_day;
        select k.door_type
          into v_Last_type
          from (select t.door_type,
                       row_number() over(order by t.rec_date desc) nums
                  from HRS_TRANSPORTED_DATA t
                 where rec_date <= v_end_day
                   and t.user_id = p_worker
                   and t.door_type in (1, 2)) k
         where k.nums = 1;
        if v_Last_type = 1 then
          insert into hrs_transported_data
          values
            (sqs_transported_data.nextval,
             v_end_day_time,
             1900,
             null,
             p_worker,
             2,
             null,
             null,
             0,
						 0
						);
        end if;
      exception
        when others then
          commit;
      end;
    END;
  END LOOP;
  COMMIT;

  RETURN;
END workereventsrecalculation;
