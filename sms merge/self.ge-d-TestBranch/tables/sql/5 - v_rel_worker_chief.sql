create or replace view v_rel_worker_chief as
select t.person  worker_pid,
       t.orgpid  worker_opid,
       t.id      worker,
       t.org,
       cw.id     chief,
       cw.orgpid chief_opid,
       cw.person chief_pid,
       0         clevel
  from slf_worker t
  left join lib_staff_schedules ss
    on ss.id = t.staff_schedule
  left join slf_worker cw
    on cw.staff_schedule = ss.chief_schedule
 WHERE (t.active = 1)
   and nvl(t.staff_schedule, 0) > 0
   and nvl( ss.chief_schedule, 0) > 0
   and nvl(cw.id, 0) > 0
   and cw.active = 1
   and t.active = 1
union all
select t.person  worker_pid,
       t.orgpid  worker_opid,
       t.id      worker,
       t.org,
       cw.id     chief,
       cw.orgpid chief_opid,
       cw.person chief_pid,
       2         clevel
  from slf_worker t
  left join lib_staff_schedules ss
    on ss.id = t.staff_schedule
  left join slf_worker cw
    on cw.staff_schedule = ss.replace_schedule
 WHERE (t.active = 1)
   and nvl(t.staff_schedule, 0) > 0
   and nvl(cw.id, 0) > 0
      and cw.active = 1
   and t.active = 1
   union all

   select t.person worker_pid,
       t.orgpid worker_opid,
       t.id     worker,
       t.org,
       0        chief,
       0        chief_opid,
       ce.chief chief_pid,
       1        clevel
  from rel_worker_chief_ext ce
  left join slf_worker t
    on ce.worker = t.id
  left join slf_persons p on p.id = ce.chief

 where (t.active = 1)
   and nvl(t.id, 0) > 0
      and p.active = 1
   and t.active = 1
