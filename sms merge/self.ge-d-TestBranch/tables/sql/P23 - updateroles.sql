CREATE OR REPLACE PROCEDURE updateroles IS
  v_result varchar2(4000) := ' ';
BEGIN
  FOR ROLES IN (SELECT * FROM lib_roles) LOOP
    BEGIN
      v_result := ' ';
      BEGIN
        FOR cur IN (SELECT rr.menu
                      FROM rel_roles_menus rr
                     WHERE rr.role = roles.id) LOOP
          BEGIN
            IF (v_result = ' ') THEN
              v_result := cur.menu;
            ELSE
              v_result := v_result || ',' || cur.menu;
            END IF;
          END;
        END LOOP;
        UPDATE lib_roles r
           SET r.lib_rel_menus = v_result
         WHERE r.id = roles.id;
      END;
    END;
  END LOOP;
  COMMIT;
END updateroles;
