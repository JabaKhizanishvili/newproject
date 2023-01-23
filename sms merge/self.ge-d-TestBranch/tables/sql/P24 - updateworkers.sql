CREATE OR REPLACE PROCEDURE updateworkers IS
  v_result varchar2(4000) := ' ';
BEGIN
  FOR worker IN (SELECT * FROM hrs_workers w WHERE w.active = 1) LOOP
    BEGIN
      v_result := ' ';
      BEGIN
        FOR cur IN (SELECT wc.worker all_chiefs
                      FROM rel_worker_chief wc
                      LEFT JOIN hrs_workers w
                        ON w.id = wc.worker
                     WHERE wc.chief = worker.id
                     ORDER BY w.firstname || ' ' || w.lastname ASC) LOOP
          BEGIN
            IF (v_result = ' ') THEN
              v_result := cur.all_chiefs;
            ELSE
              v_result := v_result || ',' || cur.all_chiefs;
            END IF;
          END;
        END LOOP; -- dbms_output.put_line(v_result);
      
        UPDATE hrs_workers w
           SET w.id = v_result
         WHERE w.id = worker.id
           AND w.ID NOT LIKE v_result;
      END;
    END; -- dbms_output.put_line(sql%rowcount);
  END LOOP;
  COMMIT;
END updateworkers;
