create or replace package body hrs_monitoring_pkg is

  procedure StartFixedMonitoring is
  begin
    insertmisseddata;
    commit;
    pkg_hrs_helper.InsertWorkersDecretTime;
    commit;
    hrs_monitoring_pkg.update_rel_worker_chief;
  end StartFixedMonitoring;

  procedure SetStartTime is
  begin
  
    for rtype in (select m.*
                    from (select t.worker,
                                 t.time_id,
                                 t.gt_day,
                                 t.gt_year,
                                 gt.*,
                                 to_date(to_char(t.real_date, 'yyyy-mm-dd') || ' ' ||
                                         gt.start_time,
                                         'yyyy-mm-dd hh24:mi') start_date,
                                 null parent_id
                            from HRS_GRAPH t
                            left join LIB_GRAPH_TIMES gt
                              on gt.id = t.time_id
                           where t.real_date >= trunc(sysdate) - 0.5
                             and t.real_date < sysdate
                          union all
                          select w.id worker,
                                 k.time_id,
                                 to_number(to_char(sysdate, 'DDD')) gt_day,
                                 to_number(to_char(sysdate, 'yyyy')) gt_year,
                                 gt.*,
                                 to_date(to_char(sysdate, 'yyyy-mm-dd') || ' ' ||
                                         gt.start_time,
                                         'yyyy-mm-dd hh24:mi') start_date,
                                 w.parent_id
                            from HRS_WORKERS_sch w
                            left join (select tt.id,
                                              decode((select hrs_monitoring_pkg.isTodayHoliday
                                                       from dual),
                                                     0,
                                                     tt.time_id,
                                                     0) time_id
                                         from (select t.id,
                                                      case
                                                        when trim(to_char(sysdate,
                                                                          'DAY')) =
                                                             'MONDAY' then
                                                         "MONDAY"
                                                        when trim(to_char(sysdate,
                                                                          'DAY')) =
                                                             'TUESDAY' then
                                                         "TUESDAY"
                                                        when trim(to_char(sysdate,
                                                                          'DAY')) =
                                                             'WEDNESDAY' then
                                                         "WEDNESDAY"
                                                        when trim(to_char(sysdate,
                                                                          'DAY')) =
                                                             'THURSDAY' then
                                                         "THURSDAY"
                                                        when trim(to_char(sysdate,
                                                                          'DAY')) =
                                                             'FRIDAY' then
                                                         "FRIDAY"
                                                        when trim(to_char(sysdate,
                                                                          'DAY')) =
                                                             'SATURDAY' then
                                                         "SATURDAY"
                                                        when trim(to_char(sysdate,
                                                                          'DAY')) =
                                                             'SUNDAY' then
                                                         "SUNDAY"
                                                      end AS time_id
                                                 from LIB_STANDARD_GRAPHS t) tt) k
                              on k.id = w.graphtype
                            left join lib_graph_times gt
                              on gt.id = k.time_id
                           where w.graphtype > 0) m
                    left join HRS_STAFF_EVENTS e
                      on e.event_date = m.start_date
                     and e.staff_id = m.worker
                   where m.start_date between
                         sysdate - hrs_monitoring_pkg.getBackTime and
                         sysdate
                     and e.id is null) loop
      begin
        begin
          if rtype.time_id > 0 then
            insert into HRS_TRANSPORTED_DATA
              (select t.ID,
                      t.REC_DATE,
                      t.ACCESS_POINT_CODE,
                      t.CARD_ID,
                      t.USER_ID,
                      t.DOOR_TYPE,
                      t.cardname,
                      t.parent_id,
                      t.c,
                      t.time_id
                 from (select null ID,
                              rtype.start_date REC_DATE,
                              null ACCESS_POINT_CODE,
                              null CARD_ID,
                              rtype.worker USER_ID,
                              hrs_monitoring_pkg.P_START_DAY_ID DOOR_TYPE,
                              '' cardname,
                              rtype.parent_id,
                              0 c,
                              0 time_id
                         from dual) t
                 left join HRS_TRANSPORTED_DATA tr
                   on t.REC_DATE = tr.REC_DATE
                  and t.USER_ID = tr.USER_ID
                  and t.DOOR_TYPE = tr.DOOR_TYPE
                where tr.id is null);
          
            /*            hrs_monitoring_pkg.InsertEvent(rtype.worker,
            hrs_monitoring_pkg.P_START_DAY_ID,
            rtype.start_date,
            null,
            rtype.time_id,
            rtype.gt_day,
            rtype.gt_year);*/
          else
            insert into HRS_TRANSPORTED_DATA
              (select t.ID,
                      t.REC_DATE,
                      t.ACCESS_POINT_CODE,
                      t.CARD_ID,
                      t.USER_ID,
                      t.DOOR_TYPE,
                      t.cardname,
                      t.parent_id,
                      t.c,
                      t.time_id
                 from (select null ID,
                              rtype.start_date REC_DATE,
                              null ACCESS_POINT_CODE,
                              null CARD_ID,
                              rtype.worker USER_ID,
                              hrs_monitoring_pkg.P_DAY_OFF_ID DOOR_TYPE,
                              '' cardname,
                              rtype.parent_id,
                              0 c,
                              0 time_id
                         from dual) t
                 left join HRS_TRANSPORTED_DATA tr
                   on t.REC_DATE = tr.REC_DATE
                  and t.USER_ID = tr.USER_ID
                  and t.DOOR_TYPE = tr.DOOR_TYPE
                where tr.id is null);
            /*            hrs_monitoring_pkg.InsertEvent(rtype.worker,
            hrs_monitoring_pkg.P_DAY_OFF_ID,
            rtype.start_date,
            '?????????',
            rtype.time_id,
            rtype.gt_day,
            rtype.gt_year);*/
          end if;
        
        end;
      end;
    end loop;
  end;

  /**
  End Time Calculation
  */

  procedure SetEndTime is
    v_result varchar2(4000);
  begin
    for rtype in (select m.*
                    from (select mm.worker,
                                 mm.time_id,
                                 mm.gt_day,
                                 mm.gt_year,
                                 case
                                   when mm.start_date > mm.end_date then
                                    mm.end_date + 1
                                   else
                                    mm.end_date
                                 end end_date,
                                 mm.parent_id
                            from (select t.worker,
                                         t.time_id,
                                         t.gt_day,
                                         t.gt_year,
                                         to_date(to_char(t.real_date,
                                                         'yyyy-mm-dd') || ' ' ||
                                                 gt.start_time,
                                                 'yyyy-mm-dd hh24:mi') start_date,
                                         to_date(to_char(t.real_date,
                                                         'yyyy-mm-dd') || ' ' ||
                                                 gt.end_time,
                                                 'yyyy-mm-dd hh24:mi') end_date,
                                         w.PARENT_ID
                                    from HRS_GRAPH t
                                    left join hrs_workers_sch w
                                      on w.ID = t.worker
                                    left join LIB_GRAPH_TIMES gt
                                      on gt.id = t.time_id
                                   where t.real_date >= trunc(sysdate) - 0.5
                                     and t.real_date < sysdate
                                  union all
                                  select w.id worker,
                                         k.time_id,
                                         to_number(to_char(sysdate, 'DDD')) gt_day,
                                         to_number(to_char(sysdate, 'yyyy')) gt_year,
                                         to_date(to_char(sysdate, 'yyyy-mm-dd') || ' ' ||
                                                 gt.start_time,
                                                 'yyyy-mm-dd hh24:mi') start_date,
                                         to_date(to_char(sysdate, 'yyyy-mm-dd') || ' ' ||
                                                 gt.end_time,
                                                 'yyyy-mm-dd hh24:mi') end_date,
                                         w.parent_id
                                    from HRS_WORKERS_sch w
                                    left join (select tt.id,
                                                      decode((select hrs_monitoring_pkg.isTodayHoliday
                                                               from dual),
                                                             0,
                                                             tt.time_id,
                                                             0) time_id
                                                 from (select t.id,
                                                              case
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'MONDAY' then
                                                                 "MONDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'TUESDAY' then
                                                                 "TUESDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'WEDNESDAY' then
                                                                 "WEDNESDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'THURSDAY' then
                                                                 "THURSDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'FRIDAY' then
                                                                 "FRIDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'SATURDAY' then
                                                                 "SATURDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'SUNDAY' then
                                                                 "SUNDAY"
                                                              end AS time_id
                                                         from LIB_STANDARD_GRAPHS t) tt) k
                                      on k.id = w.graphtype
                                    left join lib_graph_times gt
                                      on gt.id = k.time_id
                                   where w.graphtype > 0
                                  
                                  ) mm
                          
                          ) m
                    left join HRS_STAFF_EVENTS e
                      on e.event_date = m.end_date
                     and e.staff_id = m.worker
                   where m.end_date between
                         sysdate - hrs_monitoring_pkg.p_back_time and
                         sysdate
                     and e.id is null) loop
      begin
        v_result := ' ';
        begin
        
          insert into HRS_TRANSPORTED_DATA
            (select t.ID,
                    t.REC_DATE,
                    t.ACCESS_POINT_CODE,
                    t.CARD_ID,
                    t.USER_ID,
                    t.DOOR_TYPE,
                    t.cardname,
                    t.parent_id,
                    t.c,
                    t.time_id
               from (select null ID,
                            rtype.end_date REC_DATE,
                            null ACCESS_POINT_CODE,
                            null CARD_ID,
                            rtype.worker USER_ID,
                            hrs_monitoring_pkg.P_END_DAY_ID DOOR_TYPE,
                            '' cardname,
                            rtype.parent_id,
                            0 c,
                            0 time_id
                       from dual) t
               left join HRS_TRANSPORTED_DATA tr
                 on t.REC_DATE = tr.REC_DATE
                and t.USER_ID = tr.USER_ID
                and t.DOOR_TYPE = tr.DOOR_TYPE
              where tr.id is null);
          /*
          dbms_output.put_line(rtype.worker);
          dbms_output.put_line(rtype.end_date);
          hrs_monitoring_pkg.InsertEvent(rtype.worker,
                                         hrs_monitoring_pkg.P_END_DAY_ID,
                                         rtype.end_date,
                                         null,
                                         rtype.time_id,
                                         rtype.gt_day,
                                         rtype.gt_year);*/
        end;
      end;
    end loop;
  end;

  procedure SetBreakStart is
    v_result varchar2(4000);
  begin
    for rtype in (select m.*
                    from (select mm.worker,
                                 mm.time_id,
                                 mm.gt_day,
                                 mm.gt_year,
                                 case
                                   when mm.start_date > mm.start_break then
                                    mm.start_break + 1
                                   else
                                    mm.start_break
                                 end start_break,
                                 mm.parent_id
                            from (select t.worker,
                                         t.time_id,
                                         t.gt_day,
                                         t.gt_year,
                                         
                                         to_date(to_char(t.real_date,
                                                         'yyyy-mm-dd') || ' ' ||
                                                 gt.start_time,
                                                 'yyyy-mm-dd hh24:mi') start_date,
                                         to_date(to_char(t.real_date,
                                                         'yyyy-mm-dd') || ' ' ||
                                                 gt.start_break,
                                                 'yyyy-mm-dd hh24:mi') start_break,
                                         w.PARENT_ID
                                    from HRS_GRAPH t
                                    left join hrs_workers_sch w
                                      on w.ID = t.worker
                                    left join LIB_GRAPH_TIMES gt
                                      on gt.id = t.time_id
                                   where t.real_date >= trunc(sysdate) - 0.5
                                     and t.real_date < sysdate + 1
                                     and gt.start_break is not null
                                  union all
                                  select w.id worker,
                                         k.time_id,
                                         to_number(to_char(sysdate, 'DDD')) gt_day,
                                         to_number(to_char(sysdate, 'yyyy')) gt_year,
                                         to_date(to_char(sysdate, 'yyyy-mm-dd') || ' ' ||
                                                 gt.start_time,
                                                 'yyyy-mm-dd hh24:mi') start_date,
                                         to_date(to_char(sysdate, 'yyyy-mm-dd') || ' ' ||
                                                 gt.start_break,
                                                 'yyyy-mm-dd hh24:mi') start_break,
                                         w.PARENT_ID
                                    from HRS_WORKERS_sch w
                                    left join (select tt.id,
                                                      decode((select hrs_monitoring_pkg.isTodayHoliday
                                                               from dual),
                                                             0,
                                                             tt.time_id,
                                                             0) time_id
                                                 from (select t.id,
                                                              case
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'MONDAY' then
                                                                 "MONDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'TUESDAY' then
                                                                 "TUESDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'WEDNESDAY' then
                                                                 "WEDNESDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'THURSDAY' then
                                                                 "THURSDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'FRIDAY' then
                                                                 "FRIDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'SATURDAY' then
                                                                 "SATURDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'SUNDAY' then
                                                                 "SUNDAY"
                                                              end AS time_id
                                                         from LIB_STANDARD_GRAPHS t) tt) k
                                      on k.id = w.graphtype
                                    left join lib_graph_times gt
                                      on gt.id = k.time_id
                                   where w.graphtype > 0
                                     and gt.start_break is not null
                                  
                                  ) mm
                          
                          ) m
                    left join HRS_STAFF_EVENTS e
                      on e.event_date = m.start_break
                     and e.staff_id = m.worker
                   where m.start_break between sysdate and
                         sysdate - hrs_monitoring_pkg.p_back_time
                     and e.id is null) loop
      begin
        v_result := ' ';
        begin
          insert into HRS_TRANSPORTED_DATA
            (select t.ID,
                    t.REC_DATE,
                    t.ACCESS_POINT_CODE,
                    t.CARD_ID,
                    t.USER_ID,
                    t.DOOR_TYPE,
                    t.cardname,
                    t.parent_id,
                    t.c,
                    t.time_id
               from (select null ID,
                            rtype.start_break REC_DATE,
                            null ACCESS_POINT_CODE,
                            null CARD_ID,
                            rtype.worker USER_ID,
                            hrs_monitoring_pkg.P_START_BREAK_ID DOOR_TYPE,
                            '' cardname,
                            rtype.parent_id,
                            0 c,
                            0 time_id
                       from dual) t
               left join HRS_TRANSPORTED_DATA tr
                 on t.REC_DATE = tr.REC_DATE
                and t.USER_ID = tr.USER_ID
                and t.DOOR_TYPE = tr.DOOR_TYPE
              where tr.id is null);
          /*
          hrs_monitoring_pkg.InsertEvent(rtype.worker,
                                         hrs_monitoring_pkg.P_START_BREAK_ID,
                                         rtype.start_break,
                                         null,
                                         rtype.time_id,
                                         rtype.gt_day,
                                         rtype.gt_year);*/
        end;
      end;
    end loop;
  end;

  procedure SetBreakEnd is
  begin
    for rtype in (select m.*
                    from (select mm.worker,
                                 mm.time_id,
                                 mm.gt_day,
                                 mm.gt_year,
                                 case
                                   when mm.start_date > mm.end_break then
                                    mm.end_break + 1
                                   else
                                    mm.end_break
                                 end end_break,
                                 mm.parent_id
                            from (select t.worker,
                                         t.time_id,
                                         t.gt_day,
                                         t.gt_year,
                                         
                                         to_date(to_char(t.real_date,
                                                         'yyyy-mm-dd') || ' ' ||
                                                 gt.start_time,
                                                 'yyyy-mm-dd hh24:mi') start_date,
                                         to_date(to_char(t.real_date,
                                                         'yyyy-mm-dd') || ' ' ||
                                                 gt.end_break,
                                                 'yyyy-mm-dd hh24:mi') end_break,
                                         w.PARENT_ID
                                    from HRS_GRAPH t
                                    left join hrs_workers_sch w
                                      on w.ID = t.worker
                                    left join LIB_GRAPH_TIMES gt
                                      on gt.id = t.time_id
                                   where t.real_date >= trunc(sysdate) - 0.5
                                     and t.real_date < sysdate + 1
                                     and gt.end_break is not null
                                  union all
                                  select w.id worker,
                                         k.time_id,
                                         to_number(to_char(sysdate, 'DDD')) gt_day,
                                         to_number(to_char(sysdate, 'yyyy')) gt_year,
                                         to_date(to_char(sysdate, 'yyyy-mm-dd') || ' ' ||
                                                 gt.start_time,
                                                 'yyyy-mm-dd hh24:mi') start_date,
                                         to_date(to_char(sysdate, 'yyyy-mm-dd') || ' ' ||
                                                 gt.end_break,
                                                 'yyyy-mm-dd hh24:mi') end_break,
                                         w.PARENT_ID
                                    from HRS_WORKERS_sch w
                                    left join (select tt.id,
                                                      decode((select hrs_monitoring_pkg.isTodayHoliday
                                                               from dual),
                                                             0,
                                                             tt.time_id,
                                                             0) time_id
                                                 from (select t.id,
                                                              case
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'MONDAY' then
                                                                 "MONDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'TUESDAY' then
                                                                 "TUESDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'WEDNESDAY' then
                                                                 "WEDNESDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'THURSDAY' then
                                                                 "THURSDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'FRIDAY' then
                                                                 "FRIDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'SATURDAY' then
                                                                 "SATURDAY"
                                                                when trim(to_char(sysdate,
                                                                                  'DAY')) =
                                                                     'SUNDAY' then
                                                                 "SUNDAY"
                                                              end AS time_id
                                                         from LIB_STANDARD_GRAPHS t) tt) k
                                      on k.id = w.graphtype
                                    left join lib_graph_times gt
                                      on gt.id = k.time_id
                                   where w.graphtype > 0
                                     and gt.end_break is not null) mm
                          
                          ) m
                    left join HRS_STAFF_EVENTS e
                      on e.event_date = m.end_break
                     and e.staff_id = m.worker
                   where m.end_break between
                         sysdate - hrs_monitoring_pkg.p_back_time and
                         sysdate
                     and e.id is null) loop
      begin
        begin
          insert into HRS_TRANSPORTED_DATA
            (select t.ID,
                    t.REC_DATE,
                    t.ACCESS_POINT_CODE,
                    t.CARD_ID,
                    t.USER_ID,
                    t.DOOR_TYPE,
                    t.cardname,
                    t.parent_id,
                    t.c,
                    t.time_id
               from (select null ID,
                            rtype.end_break REC_DATE,
                            null ACCESS_POINT_CODE,
                            null CARD_ID,
                            rtype.worker USER_ID,
                            hrs_monitoring_pkg.P_END_BREAK_ID DOOR_TYPE,
                            '' cardname,
                            rtype.parent_id,
                            0 c,
                            0 time_id
                       from dual) t
               left join HRS_TRANSPORTED_DATA tr
                 on t.REC_DATE = tr.REC_DATE
                and t.USER_ID = tr.USER_ID
                and t.DOOR_TYPE = tr.DOOR_TYPE
              where tr.id is null);
          /*
          \*dbms_output.put_line(rtype.start_date);*\
          hrs_monitoring_pkg.InsertEvent(rtype.worker,
                                         hrs_monitoring_pkg.P_END_BREAK_ID,
                                         rtype.end_break,
                                         null,
                                         rtype.time_id,
                                         rtype.gt_day,
                                         rtype.gt_year);*/
        end;
      end;
    end loop;
  end;

  function getBackTime return number is
  begin
    return hrs_monitoring_pkg.p_back_time;
  end;

  function isTodayHoliday return number is
    v_holiday number := 0;
  begin
    select count(*)
      into v_holiday
      from (select to_date(to_char(sysdate, 'yyyy') || '-' || t.lib_month || '-' ||
                           t.lib_day,
                           'yyyy-mm-dd') holiday
              from LIB_HOLIDAYS t
            
            ) h
     where trunc(sysdate) = h.holiday;
  
    return v_holiday;
  end;

  function CalculateLateness(p_user_id number, p_date date) return number is
    v_event_date date := sysdate;
    v_diff       number := 0;
    v_minutes    number := 0;
  begin
    v_event_date := hrs_monitoring_pkg.getLastOutTime(p_user_id, p_date);
    v_diff       := (p_date - v_event_date) * 60 * 24;
    if (v_diff > 0) then
      v_minutes := v_diff;
    end if;
    return v_minutes;
  end;

  function getLastOutTime(p_user_id number, p_date date) return date is
    v_event_date date := sysdate;
  begin
    select decode(k.event_date, null, sysdate, k.event_date)
      into v_event_date
      from (select *
              from HRS_STAFF_EVENTS e
             where e.staff_id = p_user_id
               and e.event_date < p_date
               and e.real_type_id in (2, 3000, 2000)
               and e.event_date > sysdate - 1
             order by e.event_date desc) k
     where rownum < 2;
  
    return v_event_date;
  EXCEPTION
    WHEN NO_DATA_FOUND THEN
      return p_date;
  end;

  procedure update_rel_worker_chief is
  begin
    delete rel_worker_chief
     where rowid in (select t.rowid
                       from rel_worker_chief t
                       left join v_rel_worker_chief v
                         on v.worker_pid = t.worker_pid
                        and v.worker_opid = t.worker_opid
                        and v.worker = t.worker
                        and v.org = t.org
                        and v.chief = t.chief
                        and v.chief_opid = t.chief_opid
                        and v.chief_pid = t.chief_pid
                      where v.worker_pid is null);
    insert into rel_worker_chief
      select v.*
        from v_rel_worker_chief v
        left join rel_worker_chief t
          on v.worker_pid = t.worker_pid
         and v.worker_opid = t.worker_opid
         and v.worker = t.worker
         and v.org = t.org
         and v.chief = t.chief
         and v.chief_opid = t.chief_opid
         and v.chief_pid = t.chief_pid
       where t.worker_pid is null;
    commit;
  end;


end hrs_monitoring_pkg;
