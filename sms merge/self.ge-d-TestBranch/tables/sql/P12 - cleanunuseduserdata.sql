CREATE OR REPLACE PROCEDURE cleanunuseduserdata IS
BEGIN
  --// remove deleted user from graph groups delete from rel_workers_groups t where t.worker in (select t.id from hrs_workers t where t.active = -2);

  COMMIT;
  DELETE FROM hrs_graph t
   WHERE (t.worker, t.gt_day, t.gt_year, t.time_id, t.real_date) IN
         (SELECT g.worker, g.gt_day, g.gt_year, g.time_id, g.real_date
            FROM hrs_graph g
            LEFT JOIN lib_graph_times gt
              ON gt.id = g.time_id
            LEFT JOIN hrs_workers w
              ON w.id = g.worker
           WHERE g.real_date >= trunc(sysdate)
             AND w.active < 1);
  COMMIT;
  updategroups;
  updateroles;
  updateworkers;
END cleanunuseduserdata;