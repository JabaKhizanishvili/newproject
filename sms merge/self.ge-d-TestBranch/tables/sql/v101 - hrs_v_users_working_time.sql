create or replace view hrs_v_users_working_time as
select gd."WORKER",
       gd."REAL_DATE",
       gd."TIME_ID",
       to_date(to_char(gd.real_date, 'yyyy-mm-dd') || ' ' || gt.start_time,
               'yyyy-mm-dd hh24:mi') start_time,

       CASE
         WHEN replace(gt.end_time, ':', '') <=
              replace(gt.start_time, ':', '') then
          to_date(to_char(gd.real_date + 1, 'yyyy-mm-dd') || ' ' ||
                  gt.end_time,
                  'yyyy-mm-dd hh24:mi')
         else
          to_date(to_char(gd.real_date, 'yyyy-mm-dd') || ' ' || gt.end_time,
                  'yyyy-mm-dd hh24:mi')
       end end_time,
       CASE
         WHEN replace(gt.end_time, ':', '') <=
              replace(gt.start_time, ':', '') then
          to_date(to_char(gd.real_date + 1, 'yyyy-mm-dd') || ' ' ||
                  gt.end_time,
                  'yyyy-mm-dd hh24:mi') + 1 / 2
         else
          to_date(to_char(gd.real_date, 'yyyy-mm-dd') || ' ' || gt.end_time,
                  'yyyy-mm-dd hh24:mi') + 1 / 2
       end day_end

  from (select w.id worker, g.real_date, g.time_id
          from hrs_workers_sch w
          left join hrs_graph g
            on g.worker = w.id
         where w.graphtype = 0
           and w.active > 0
           and g.real_date between trunc(sysdate) - 1 and sysdate
        union all
        select w.id worker,
               r.mdate,
               case
                 when trim(to_char(r.mdate, 'DAY')) = 'MONDAY' then
                  s.MONDAY
                 when trim(to_char(r.mdate, 'DAY')) = 'TUESDAY' then
                  s.TUESDAY
                 when trim(to_char(r.mdate, 'DAY')) = 'WEDNESDAY' then
                  s.WEDNESDAY
                 when trim(to_char(r.mdate, 'DAY')) = 'THURSDAY' then
                  s.THURSDAY
                 when trim(to_char(r.mdate, 'DAY')) = 'FRIDAY' then
                  s.FRIDAY
                 when trim(to_char(r.mdate, 'DAY')) = 'SATURDAY' then
                  s.SATURDAY
                 when trim(to_char(r.mdate, 'DAY')) = 'SUNDAY' then
                  s.SUNDAY
               end AS time_id
          from hrs_workers_sch w
          left join LIB_STANDARD_GRAPHS s
            on s.id = w.graphtype
          LEFT JOIN (select trunc(sysdate) - 1 mdate
                       from dual
                     union all
                     select trunc(sysdate) mdate from dual) r
            on 1 = 1
         where w.graphtype > 0) gd
  left join lib_graph_times gt
    on gt.id = gd.time_id 
where gt.type=0
