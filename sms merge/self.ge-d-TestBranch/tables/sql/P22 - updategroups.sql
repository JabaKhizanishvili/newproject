CREATE OR REPLACE PROCEDURE updategroups IS
  v_result varchar2(4000) := ' ';
BEGIN
  FOR groups IN (SELECT * FROM lib_workers_groups) LOOP
    BEGIN
      v_result := ' ';
      BEGIN
        FOR cur IN (SELECT wg.worker
                      FROM rel_workers_groups wg
                     WHERE wg.group_id = groups.id
                     ORDER BY wg.ordering ASC) LOOP
          BEGIN
            IF (v_result = ' ') THEN
              v_result := cur.worker;
            ELSE
              v_result := v_result || ',' || cur.worker;
            END IF;
          END;
        END LOOP;
        UPDATE lib_workers_groups r
           SET r.workers = v_result
         WHERE r.id = groups.id;
      END;
    END;
  END LOOP;
  COMMIT;
END updategroups;
