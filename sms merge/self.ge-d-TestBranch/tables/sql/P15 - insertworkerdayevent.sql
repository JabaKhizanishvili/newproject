CREATE OR REPLACE PROCEDURE insertworkerdayevent(p_date_start date,
                                                 p_date_end   date,
                                                 p_worker     number) IS
  hrs_user_day_start    date := NULL;
  hrs_user_day_end      date := NULL;
  hrs_user_breack_start date := NULL;
  hrs_user_breack_end   date := NULL;
  hrs_user_time_id      number := 0;
  p_day_off_id          number := 1500;
  p_start_day_id        number := 2000;
  p_start_break_id      number := 2500;
  p_end_break_id        number := 3000;
  p_end_day_id          number := 3500;
  v_rest_type           number := 0;
  v_rest_minutes        number := 0;
BEGIN
  FOR v_day IN (SELECT (trunc(p_date_start) + LEVEL - 1) pdate
                  FROM dual
                CONNECT BY LEVEL <=
                           trunc(p_date_end) - trunc(p_date_start) + 1) LOOP
    BEGIN
      pkg_hrs_helper.getuserdaytimes(p_worker,
                                     v_day.pdate,
                                     hrs_user_day_start,
                                     hrs_user_day_end,
                                     hrs_user_breack_start,
                                     hrs_user_breack_end,
                                     hrs_user_time_id,
                                     v_rest_type,
                                     v_rest_minutes);
    
      IF hrs_user_time_id > 0 THEN
        IF hrs_user_day_start < sysdate THEN
          INSERT INTO hrs_transported_data
            (SELECT t.id,
                    t.rec_date,
                    t.access_point_code,
                    t.card_id,
                    t.user_id,
                    t.door_type,
                    t.cardname,
                    t.parent_id,
                    t.client_id,
										t.time_id
               FROM (SELECT NULL id,
                            hrs_user_day_start rec_date,
                            NULL access_point_code,
                            NULL card_id,
                            p_worker user_id,
                            p_start_day_id door_type,
                            '' cardname,
                            null parent_id,
                            0 client_id,
														0 time_id
                       FROM dual) t
               LEFT JOIN hrs_transported_data tr
                 ON t.rec_date = tr.rec_date
                AND t.user_id = tr.user_id
                AND t.door_type = tr.door_type
              WHERE tr.id IS NULL);
        
        END IF;
      
        COMMIT;
      
        IF hrs_user_breack_start IS NOT NULL THEN
          IF hrs_user_breack_start < sysdate THEN
            INSERT INTO hrs_transported_data
              (SELECT t.id,
                      t.rec_date,
                      t.access_point_code,
                      t.card_id,
                      t.user_id,
                      t.door_type,
                      t.cardname,
                      t.parent_id,
                      t.client_id,
											t.time_id
                 FROM (SELECT NULL id,
                              hrs_user_breack_start rec_date,
                              NULL access_point_code,
                              NULL card_id,
                              p_worker user_id,
                              p_start_break_id door_type,
                              '' cardname,
                              null parent_id,
                              0 client_id,
															0 time_id
                         FROM dual) t
                 LEFT JOIN hrs_transported_data tr
                   ON t.rec_date = tr.rec_date
                  AND t.user_id = tr.user_id
                  AND t.door_type = tr.door_type
                WHERE tr.id IS NULL);
          
          END IF;
        
          COMMIT;
        
        END IF;
      
        IF hrs_user_breack_end IS NOT NULL THEN
          IF hrs_user_breack_end < sysdate THEN
            INSERT INTO hrs_transported_data
              (SELECT t.id,
                      t.rec_date,
                      t.access_point_code,
                      t.card_id,
                      t.user_id,
                      t.door_type,
                      t.cardname,
                      t.parent_id,
                      t.client_id,
											t.time_id
                 FROM (SELECT NULL id,
                              hrs_user_breack_end rec_date,
                              NULL access_point_code,
                              NULL card_id,
                              p_worker user_id,
                              p_end_break_id door_type,
                              '' cardname,
                              null parent_id,
                              0 client_id,
															0 time_id
                         FROM dual) t
                 LEFT JOIN hrs_transported_data tr
                   ON t.rec_date = tr.rec_date
                  AND t.user_id = tr.user_id
                  AND t.door_type = tr.door_type
                WHERE tr.id IS NULL);
          
            COMMIT;
          
          END IF;
        
        END IF;
      
        IF hrs_user_day_end < sysdate THEN
          INSERT INTO hrs_transported_data
            (SELECT t.id,
                    t.rec_date,
                    t.access_point_code,
                    t.card_id,
                    t.user_id,
                    t.door_type,
                    t.cardname,
                    t.parent_id,
                    t.client_id,
										t.time_id
               FROM (SELECT NULL id,
                            hrs_user_day_end rec_date,
                            NULL access_point_code,
                            NULL card_id,
                            p_worker user_id,
                            p_end_day_id door_type,
                            '' cardname,
                            null parent_id,
                            0 client_id,
														0 time_id
                       FROM dual) t
               LEFT JOIN hrs_transported_data tr
                 ON t.rec_date = tr.rec_date
                AND t.user_id = tr.user_id
                AND t.door_type = tr.door_type
              WHERE tr.id IS NULL);
        
          COMMIT;
        
        END IF;
      
        pkg_hrs_helper.insertworkersdecrettime(v_day.pdate);
      
        COMMIT;
      
      ELSE
        IF trunc(v_day.pdate) < sysdate THEN
          INSERT INTO hrs_transported_data
            (SELECT t.id,
                    t.rec_date,
                    t.access_point_code,
                    t.card_id,
                    t.user_id,
                    t.door_type,
                    t.cardname,
                    t.parent_id,
                    t.client_id,
										t.time_id
               FROM (SELECT NULL id,
                            trunc(v_day.pdate) rec_date,
                            NULL access_point_code,
                            NULL card_id,
                            p_worker user_id,
                            p_day_off_id door_type,
                            '' cardname,
                            null parent_id,
                            0 client_id,
														0 time_id
                       FROM dual) t
               LEFT JOIN hrs_transported_data tr
                 ON t.rec_date = tr.rec_date
                AND t.user_id = tr.user_id
                AND t.door_type = tr.door_type
              WHERE tr.id IS NULL);
        
          COMMIT;
        
        END IF;
      
      END IF;
    
      -- return;
    END;
  
  END LOOP;

END insertworkerdayevent;
