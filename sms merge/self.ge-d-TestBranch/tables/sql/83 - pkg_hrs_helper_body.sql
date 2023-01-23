create or replace package body pkg_hrs_helper is

  procedure getUserDayTimes(p_user_id        number,
                            p_date           date,
                            v_start          out date,
                            v_end            out date,
                            v_bstart         out date,
                            v_bend           out date,
                            hrs_user_time_id out number,
                            v_rest_type      out number,
                            v_rest_minutes   out number) is
    v_start_date  varchar2(400) := null;
    v_end_date    varchar2(400) := null;
    v_bstart_date varchar2(400) := null;
    v_bend_date   varchar2(400) := null;
  begin
    pkg_hrs_helper.getUserGraphDates(p_user_id,
                                     p_date,
                                     v_start_date,
                                     v_end_date,
                                     v_bstart_date,
                                     v_bend_date,
                                     hrs_user_time_id,
                                     v_rest_type,
                                     v_rest_minutes);
  
    -- dbms_output.put_line(v_start_date);
    if trim(v_start_date) is not null then
      begin
        select to_date(to_char(p_date, 'yyyy-mm-dd') || ' ' || v_start_date,
                       'yyyy-mm-dd hh24:mi')
          into v_start
          from dual;
      exception
        when others then
          v_start := null;
      end;
    end if;
  
    if trim(v_end_date) is not null then
      begin
        select to_date(to_char(p_date, 'yyyy-mm-dd') || ' ' || v_end_date,
                       'yyyy-mm-dd hh24:mi')
          into v_end
          from dual;
      exception
        when others then
          v_end := null;
      end;
    end if;
  
    if trim(v_bstart_date) is not null then
      begin
        select to_date(to_char(p_date, 'yyyy-mm-dd') || ' ' ||
                       v_bstart_date,
                       'yyyy-mm-dd hh24:mi')
          into v_bstart
          from dual;
      exception
        when others then
          v_start := null;
      end;
    end if;
  
    if trim(v_bend_date) is not null then
      begin
        select to_date(to_char(p_date, 'yyyy-mm-dd') || ' ' || v_bend_date,
                       'yyyy-mm-dd hh24:mi')
          into v_bend
          from dual;
      exception
        when others then
          v_end := null;
      end;
    end if;
  
    if v_start > v_end then
      v_end := v_end + 1;
    end if;
  
    if v_bstart > v_bend then
      v_bend := v_bend + 1;
    end if;
  end;

  procedure getUserDayTimesNew(p_user_id        number,
                               p_date           date,
                               v_start          out date,
                               v_end            out date,
                               v_bstart         out date,
                               v_bend           out date,
                               hrs_user_time_id out number,
                               v_rest_type      out number,
                               v_rest_minutes   out number,
                               timecontrol      out number) is
  begin
    begin
    
      select m.*
        into v_start,
             v_end,
             v_bstart,
             v_bend,
             hrs_user_time_id,
             v_rest_type,
             v_rest_minutes,
             timecontrol
        from (select to_date(to_char(t.real_date, 'yyyy-mm-dd') || ' ' ||
                             gt.start_time,
                             'yyyy-mm-dd hh24:mi') start_time,
                     
                     CASE
                       WHEN replace(gt.end_time, ':', '') <=
                            replace(gt.start_time, ':', '') then
                        to_date(to_char(t.real_date + 1, 'yyyy-mm-dd') || ' ' ||
                                gt.end_time,
                                'yyyy-mm-dd hh24:mi')
                       else
                        to_date(to_char(t.real_date, 'yyyy-mm-dd') || ' ' ||
                                gt.end_time,
                                'yyyy-mm-dd hh24:mi')
                     end end_time,
                     CASE
                       WHEN gt.start_break is not null and
                            replace(gt.start_break, ':', '') <=
                            replace(gt.start_time, ':', '') then
                        to_date(to_char(t.real_date + 1, 'yyyy-mm-dd') || ' ' ||
                                gt.start_break,
                                'yyyy-mm-dd hh24:mi')
                       WHEN gt.start_break is not null and
                            replace(gt.start_break, ':', '') >
                            replace(gt.start_time, ':', '') then
                        to_date(to_char(t.real_date, 'yyyy-mm-dd') || ' ' ||
                                gt.start_break,
                                'yyyy-mm-dd hh24:mi')
                       else
                        null
                     end start_break,
                     CASE
                       WHEN gt.end_break is not null and
                            replace(gt.end_break, ':', '') <=
                            replace(gt.start_time, ':', '') then
                        to_date(to_char(t.real_date + 1, 'yyyy-mm-dd') || ' ' ||
                                gt.end_break,
                                'yyyy-mm-dd hh24:mi')
                       WHEN gt.end_break is not null and
                            replace(gt.end_break, ':', '') >
                            replace(gt.start_time, ':', '') then
                        to_date(to_char(t.real_date, 'yyyy-mm-dd') || ' ' ||
                                gt.end_break,
                                'yyyy-mm-dd hh24:mi')
                       else
                        null
                     end end_break,
                     t.time_id,
                     gt.rest_type,
                     gt.rest_minutes,
                     w.timecontrol
              
                from (select g.worker id, g.real_date, g.time_id
                        from hrs_graph g
                        left join slf_worker w
                          on w.id = g.worker
                       where g.worker = p_user_id
                         and w.graphtype = 0
                         and g.real_date between trunc(p_date) - 2 and p_date
                      union all
                      select w.id worker,
                             r.mdate,
                             nvl(case
                                   when trim(to_char(r.mdate, 'DAY')) = 'MONDAY' then
                                    s.MONDAY
                                   when trim(to_char(r.mdate, 'DAY')) = 'TUESDAY' then
                                    s.TUESDAY
                                   when trim(to_char(r.mdate, 'DAY')) =
                                        'WEDNESDAY' then
                                    s.WEDNESDAY
                                   when trim(to_char(r.mdate, 'DAY')) = 'THURSDAY' then
                                    s.THURSDAY
                                   when trim(to_char(r.mdate, 'DAY')) = 'FRIDAY' then
                                    s.FRIDAY
                                   when trim(to_char(r.mdate, 'DAY')) = 'SATURDAY' then
                                    s.SATURDAY
                                   when trim(to_char(r.mdate, 'DAY')) = 'SUNDAY' then
                                    s.SUNDAY
                                 end,
                                 0) time_id
                        from slf_worker w
                        left join slf_persons p
                          on p.id = w.person
                        left join LIB_STANDARD_GRAPHS s
                          on s.id = w.graphtype
                        LEFT JOIN (select trunc(p_date) - 1 mdate
                                     from dual
                                   union all
                                   select trunc(p_date) mdate from dual) r
                          on 1 = 1
                       where w.id = p_user_id) t
                left join lib_graph_times gt
                  on gt.id = t.time_id
                left join slf_worker ww
                  on ww.id = t.id
                left join slf_persons w
                  on w.id = ww.person) m
       where m.time_id > 0
         and p_date between m.start_time and m.end_time;
    exception
      when others then
        v_start := null;
    end;
  end;

  procedure getUserGraphDates(p_user_id          in number,
                              p_date             in date,
                              v_start_date       out varchar2,
                              v_end_date         out varchar2,
                              v_bstart_date      out varchar2,
                              v_bend_date        out varchar2,
                              user_graph_time_id out number,
                              v_rest_type        out number,
                              v_rest_minutes     out number
                              
                              ) is
    user_graph_type number := 0;
  begin
    v_start_date   := null;
    v_end_date     := null;
    v_bstart_date  := null;
    v_bend_date    := null;
    v_rest_type    := 0;
    v_rest_minutes := 0;
    begin
      select w.graphtype
        into user_graph_type
        from slf_worker w
       where w.id = p_user_id;
    exception
      when others then
        return;
    end;
    if user_graph_type = 0 then
      user_graph_time_id := pkg_hrs_helper.getUserTimeIDByDinGraph(p_user_id,
                                                                   p_date);
    else
      user_graph_time_id := pkg_hrs_helper.getUserTimeIDByGraph(user_graph_type,
                                                                p_date);
    end if;
    --    dbms_output.put_line(user_graph_time_id);
    begin
      select g.start_time,
             g.end_time,
             g.start_break,
             g.end_break,
             g.rest_type,
             g.rest_minutes
        into v_start_date,
             v_end_date,
             v_bstart_date,
             v_bend_date,
             v_rest_type,
             v_rest_minutes
        from lib_graph_times g
       where g.id = user_graph_time_id;
    exception
      when others then
        return;
    end;
  
  end;

  function getUserTimeIDByGraph(p_graph_type number, p_date date)
    return number is
    v_Result number := 0;
  begin
    begin
      --  dbms_output.put_line(p_graph_type);
      select decode((select pkg_hrs_helper.isHoliday(p_date) from dual),
                    0,
                    tt.time_id,
                    0) time_id
        into v_Result
        from (select t.id,
                     case
                       when trim(to_char(p_date, 'DAY')) = 'MONDAY' then
                        "MONDAY"
                       when trim(to_char(p_date, 'DAY')) = 'TUESDAY' then
                        "TUESDAY"
                       when trim(to_char(p_date, 'DAY')) = 'WEDNESDAY' then
                        "WEDNESDAY"
                       when trim(to_char(p_date, 'DAY')) = 'THURSDAY' then
                        "THURSDAY"
                       when trim(to_char(p_date, 'DAY')) = 'FRIDAY' then
                        "FRIDAY"
                       when trim(to_char(p_date, 'DAY')) = 'SATURDAY' then
                        "SATURDAY"
                       when trim(to_char(p_date, 'DAY')) = 'SUNDAY' then
                        "SUNDAY"
                     end AS time_id
                from LIB_STANDARD_GRAPHS t
               where t.id = p_graph_type) tt;
    exception
      when others then
        v_Result := 0;
    end;
    return v_Result;
  end;

  function getUserTimeIDByDinGraph(p_user_id number, p_date date)
    return number is
    v_Result number := 0;
  begin
    begin
      select t.time_id
        into v_Result
        from HRS_GRAPH t
       where t.real_date = trunc(p_date)
         and t.worker = p_user_id;
    exception
      when others then
        v_Result := 0;
    end;
  
    return v_Result;
  end;
  procedure getUsedRestMinutes(p_start_date        date,
                               p_end_date          date,
                               p_worker            number,
                               v_rest_minutes      number,
                               TrueLatenessMinutes out number,
                               EventType           out number,
                               LatenessMinutes     in out number,
                               LatenessReason      in out varchar2) is
    v_Result number := 0;
    v_Rem    number := 0;
    v_reason varchar2(400);
  begin
    begin
      select nvl(sum(e.true_time_min), 0)
        into v_Result
        from hrs_staff_events e
       where e.staff_id = p_worker
         and e.event_date between p_start_date and p_end_date
         and e.event_type = 3;
    exception
      when others then
        v_Result := 0;
    end;
    v_Rem := v_rest_minutes - v_Result;
    if v_Rem < 0 then
      v_Rem := 0;
    end if;
  
    if v_Rem >= LatenessMinutes then
      TrueLatenessMinutes := LatenessMinutes;
      LatenessMinutes     := 0;
    else
      TrueLatenessMinutes := v_Rem;
      LatenessMinutes     := LatenessMinutes - v_Rem;
    end if;
  
    if TrueLatenessMinutes > 0 then
      EventType := 3;
      select t.lib_title
        into v_reason
        from LIB_APPLICATIONS_TYPES t
       where t.type = 10;
      LatenessReason := v_reason || ' ( ' || TrueLatenessMinutes || ' )';
    end if;
  end;

  function isHoliday(p_date date) return number is
    v_holiday number := 0;
  begin
    select count(*)
      into v_holiday
      from (select to_date(to_char(p_date, 'yyyy') || '-' || t.lib_month || '-' ||
                           t.lib_day,
                           'yyyy-mm-dd') holiday
              from LIB_HOLIDAYS t
             where t.active = 1) h
     where trunc(p_date) = h.holiday;
  
    return v_holiday;
  end;

  function isTomorrowUserHoliday(p_worker number, p_date date) return number is
    v_holiday            number := 0;
    hrs_user_day_start   date := null;
    hrs_user_day_end     date := null;
    hrs_user_break_start date := null;
    hrs_user_break_end   date := null;
    hrs_user_time_id     number := 0;
    v_rest_type          number := 0;
    v_rest_minutes       number := 0;
  begin
    begin
      select count(*)
        into v_holiday
        from (select to_date(to_char(p_date, 'yyyy') || '-' || t.lib_month || '-' ||
                             t.lib_day,
                             'yyyy-mm-dd') holiday
                from LIB_HOLIDAYS t
              
              ) h
       where trunc(p_date + 1) = h.holiday;
    exception
      when others then
        v_holiday := 0;
    end;
    if v_holiday > 0 then
      return v_holiday;
    end if;
    pkg_hrs_helper.getUserDayTimes(p_worker,
                                   trunc(p_date) + 1,
                                   hrs_user_day_start,
                                   hrs_user_day_end,
                                   hrs_user_break_start,
                                   hrs_user_break_end,
                                   hrs_user_time_id,
                                   v_rest_type,
                                   v_rest_minutes);
    if hrs_user_time_id = 0 then
      v_holiday := 1;
    end if;
    return v_holiday;
  end;

  function getWorkerLastOUTDate(v_stuff_id number, p_date date) return date is
    return_value date;
  begin
    begin
      select rd.event_date
        into return_value
        from hrs_staff_events rd
       where rd.id =
             (select max(u.id)
                from hrs_staff_events u
               where u.staff_id = v_stuff_id
                 and u.event_date <= p_date
                 and u.real_type_id in
                     (SELECT trim(COLUMN_VALUE) text
                        FROM xmltable(pkg_hrs_helper.getOutDoorsIDx)));
    exception
      when NO_DATA_FOUND then
        return null;
    end;
    return return_value;
  end;

  function getWorkerLastINDate(v_stuff_id number, p_date date) return date is
    return_value date;
  begin
    begin
      select rd.event_date
        into return_value
        from hrs_staff_events rd
       where rd.id =
             (select max(u.id)
                from hrs_staff_events u
               where u.staff_id = v_stuff_id
                 and u.event_date <= p_date
                 and u.real_type_id in
                     (SELECT trim(COLUMN_VALUE) text
                        FROM xmltable(pkg_hrs_helper.getInDoorsIDx)));
    exception
      when NO_DATA_FOUND then
        return null;
    end;
    return return_value;
  end;

  function getWorkerLastInVirtDate(v_stuff_id number, p_date date)
    return date is
    return_value date;
  begin
    begin
      select rd.event_date
        into return_value
        from hrs_staff_events rd
       where rd.id =
             (select max(u.id)
                from hrs_staff_events u
               where u.staff_id = v_stuff_id
                 and u.event_date <= p_date
                 and u.real_type_id in
                     (SELECT trim(COLUMN_VALUE) text
                        FROM xmltable(pkg_hrs_helper.getInVirtIDx())));
    exception
      when NO_DATA_FOUND then
        return null;
    end;
    return return_value;
  end;

  function getWorkerLastOutVirtDate(v_stuff_id number, p_date date)
    return date is
    return_value date;
  begin
    begin
      select rd.event_date
        into return_value
        from hrs_staff_events rd
       where rd.id =
             (select max(u.id)
                from hrs_staff_events u
               where u.staff_id = v_stuff_id
                 and u.event_date <= p_date
                 and u.real_type_id in
                     (SELECT trim(COLUMN_VALUE) text
                        FROM xmltable(pkg_hrs_helper.getOutVirtIDx())));
    exception
      when NO_DATA_FOUND then
        return null;
    end;
    return return_value;
  end;

  function getOutDoorsIDx return varchar2 is
    return_value varchar2(4000);
  begin
    return_value := '2,11';
    return return_value;
  end;

  function getOutVirtIDx return varchar2 is
    return_value varchar2(4000);
  begin
    return_value := '2500,3500';
    return return_value;
  end;

  function getInDoorsIDx return varchar2 is
    return_value varchar2(4000);
  begin
    return_value := '1,10';
    return return_value;
  end;

  function getInVirtIDx return varchar2 is
    return_value varchar2(4000);
  begin
    return_value := '4500';
    return return_value;
  end;

  function getLimitedOutTypes return varchar2 is
    return_value varchar2(4000);
  begin
    return_value := '2, 6, 8';
    return return_value;
  end;
  procedure StartPositiveReason(p_staf_id    number,
                                p_Start_Date Date,
                                p_day_end    date) is
    has_positive_reason number := 0;
  begin
    select count(*)
      into has_positive_reason
      from hrs_applications t
     where t.type in
           (SELECT trim(COLUMN_VALUE) text
              FROM xmltable(pkg_hrs_helper.getLimitedOutTypes()))
       and t.worker = p_staf_id
       and t.status > 0
       and p_Start_Date between t.start_date and t.end_date;
  
    if (has_positive_reason > 0) then
      update hrs_applications t
         set t.start_date = p_Start_Date, t.end_date = p_day_end
       where t.type in
             (SELECT trim(COLUMN_VALUE) text
                FROM xmltable(pkg_hrs_helper.getLimitedOutTypes()))
         and t.worker = p_staf_id
         and t.status > 0
         and p_Start_Date between t.start_date and t.end_date;
    end if;
  end;

  procedure ENDPositiveReason(p_staf_id number, p_Start_Date Date) is
    has_positive_reason number := 0;
  begin
    select count(*)
      into has_positive_reason
      from hrs_applications t
     where t.type in
           (SELECT trim(COLUMN_VALUE) text
              FROM xmltable(pkg_hrs_helper.getLimitedOutTypes()))
       and t.worker = p_staf_id
       and t.status > 0
       and p_Start_Date between t.start_date and t.end_date;
  
    /*select count(*)
     into has_positive_reason
     from hrs_applications t
    where t.type in
          (SELECT trim(COLUMN_VALUE) text
             FROM xmltable(pkg_hrs_helper.getLimitedOutTypes()))
      and t.worker = p_staf_id
      and t.status > 0
      and p_Start_Date >= t.start_date
      and t.id not in (select e.app_id
                         from hrs_staff_events e
                        where e.staff_id = p_staf_id)*/
  
    if (has_positive_reason > 0) then
      update hrs_applications t
         set t.end_date = p_Start_Date
       where t.type in
             (SELECT trim(COLUMN_VALUE) text
                FROM xmltable(pkg_hrs_helper.getLimitedOutTypes()))
         and t.worker = p_staf_id
         and t.status > 0
         and p_Start_Date between t.start_date and t.end_date;
    end if;
  end;

  procedure getPositiveReason(p_user_id     in number,
                              p_date        in date,
                              v_result      out number,
                              v_reason      out varchar2,
                              v_more_reason out varchar2,
                              v_type_name   out varchar2,
                              v_type        out number) is
  
  begin
    begin
      --  dbms_output.put_line(p_user_id);
      select ap.id, ap.lib_title, ap.info, ap.ucomment, ap.type
        into v_result, v_type_name, v_reason, v_more_reason, v_type
        from (select a.id, at.lib_title, a.info, a.ucomment, a.type
                from hrs_applications a
                left join v_lib_applications_types at
                  on a.type = at.type
               where a.worker = p_user_id
                 and a.status > 0
                 and a.start_date < a.end_date
                 and p_date between a.start_date and a.end_date
               order by a.start_date asc) ap
       where rownum = 1
      
      ;
    exception
      when others then
        v_result      := 0;
        v_reason      := null;
        v_more_reason := null;
        v_type_name   := null;
        v_type        := null;
    end;
  end;

  procedure stopBulletin(p_worker number, p_date date) is
    v_bull_count number := 0;
  begin
    select count(1)
      into v_bull_count
      from hrs_applications a
     where a.worker = p_worker
       and a.type = 5
       and a.status = 1
       and p_date between a.start_date and a.end_date;
    if v_bull_count > 0 then
      update hrs_applications a
         set a.end_date = trunc(p_date) - 1 / 24 / 60 / 60, a.status = 2
       where a.worker = p_worker
         and a.type = 5
         and a.status = 1
         and p_date between a.start_date and a.end_date;
    end if;
  end;

  function getWorkerLastEventType(p_worker number, p_date date) return number is
    v_event_type number := 2;
  begin
    begin
      select rd.real_type_id
        into v_event_type
        from hrs_staff_events rd
       where rd.id = (select max(u.id)
                        from hrs_staff_events u
                       where u.staff_id = p_worker
                         and u.event_date <= p_date
                         and u.real_type_id in (1, 2));
    exception
      when NO_DATA_FOUND then
        v_event_type := 2;
    end;
    return v_event_type;
  end;

  function getWorkerLastEventDate(p_worker number, p_date date) return date is
    v_event_type date := null;
  begin
    begin
      select rd.event_date
        into v_event_type
        from hrs_staff_events rd
       where rd.id = (select max(u.id)
                        from hrs_staff_events u
                       where u.staff_id = p_worker
                         and u.event_date <= p_date
                         and u.real_type_id in (1, 2));
    exception
      when NO_DATA_FOUND then
        v_event_type := null;
    end;
    return v_event_type;
  end;
  function HaveWorkerStandardGraph(p_worker number) return number is
    v_return number := 0;
  begin
    begin
      select w.graphtype
        into v_return
        from hrs_workers_sch w
       where w.id = p_worker;
    exception
      when others then
        return v_return;
    end;
    return v_return;
  end;
  function UserHasDecretTime(p_worker number, p_date date) return number is
    v_decret             number := 0;
    v_decret_type        number := 0;
    hrs_user_day_start   date := null;
    hrs_user_day_end     date := null;
    hrs_user_break_start date := null;
    hrs_user_break_end   date := null;
    hrs_user_time_id     number := 0;
    v_rest_type          number := 0;
    v_rest_minutes       number := 0;
  begin
    begin
      select count(*), h.type
        into v_decret, v_decret_type
        from hrs_decret_hour h
       where trunc(p_date) between h.start_date and h.end_date
       group by h.type;
    exception
      when others then
        v_decret := 0;
    end;
    if v_decret = 0 then
      return v_decret;
    end if;
    if v_decret_type = 0 then
      return v_decret;
    end if;
  
    return 1;
    pkg_hrs_helper.getUserDayTimes(p_worker,
                                   trunc(p_date),
                                   hrs_user_day_start,
                                   hrs_user_day_end,
                                   hrs_user_break_start,
                                   hrs_user_break_end,
                                   hrs_user_time_id,
                                   v_rest_type,
                                   v_rest_minutes);
    if hrs_user_time_id = 0 then
      v_decret := 1;
    end if;
    return v_decret;
  end;

  procedure getUserDecretTimes(p_user_id number,
                               p_date    date,
                               p_type    number,
                               v_start   out date,
                               v_end     out date) is
    p_decret_minutes     number := 0;
    p_decret_hour        number := 0;
    hrs_user_day_start   date := null;
    hrs_user_day_end     date := null;
    hrs_user_break_start date := null;
    hrs_user_break_end   date := null;
    hrs_user_time_id     number := 0;
    v_rest_type          number := 0;
    v_rest_minutes       number := 0;
  begin
    pkg_hrs_helper.getUserDayTimes(p_user_id,
                                   p_date,
                                   hrs_user_day_start,
                                   hrs_user_day_end,
                                   hrs_user_break_start,
                                   hrs_user_break_end,
                                   hrs_user_time_id,
                                   v_rest_type,
                                   v_rest_minutes);
    p_decret_minutes := getconfig('hr_decret_minutes');
    p_decret_hour    := (1 / 24 / 60 * p_decret_minutes);
    case
      when p_type = 1 then
        v_start := hrs_user_day_start;
        v_end   := hrs_user_day_start + p_decret_hour;
      when p_type = 2 then
        v_start := hrs_user_break_end;
        v_end   := hrs_user_break_end + p_decret_hour;
      when p_type = 3 then
        v_start := hrs_user_day_end - p_decret_hour;
        v_end   := hrs_user_day_end;
    end case;
    return;
    /*
    if trim(v_start_date) is not null then
      begin
        select to_date(to_char(p_date, 'yyyy-mm-dd') || ' ' || v_start_date,
                       'yyyy-mm-dd hh24:mi')
          into v_start
          from dual;
      exception
        when others then
          v_start := null;
      end;
    end if;
    
    if trim(v_end_date) is not null then
      begin
        select to_date(to_char(p_date, 'yyyy-mm-dd') || ' ' || v_end_date,
                       'yyyy-mm-dd hh24:mi')
          into v_end
          from dual;
      exception
        when others then
          v_end := null;
      end;
    end if;
    
    if trim(v_bstart_date) is not null then
      begin
        select to_date(to_char(p_date, 'yyyy-mm-dd') || ' ' ||
                       v_bstart_date,
                       'yyyy-mm-dd hh24:mi')
          into v_bstart
          from dual;
      exception
        when others then
          v_start := null;
      end;
    end if;
    
    if trim(v_bend_date) is not null then
      begin
        select to_date(to_char(p_date, 'yyyy-mm-dd') || ' ' || v_bend_date,
                       'yyyy-mm-dd hh24:mi')
          into v_bend
          from dual;
      exception
        when others then
          v_end := null;
      end;
    end if;
    
    if v_start > v_end then
      v_end := v_end + 1;
    end if;
    
    if v_bstart > v_bend then
      v_bend := v_bend + 1;
    end if;*/
  end;
  procedure InsertWorkersDecretTime(p_date date := null) is
    v_date  date := sysdate;
    v_start date;
    v_end   date;
    v_id    number := 0;
  begin
    if p_date is not null then
      v_date := p_date;
    end if;
    for v_user in (select t.*, w.parent_id, w.client_id cl_id
                     from hrs_decret_hour t
                     left join hrs_workers_sch w
                       on w.id = t.worker
                    WHERE t.status > 0
                      and v_date between t.start_date and t.end_date) loop
      begin
        begin
          getUserDecretTimes(v_user.worker,
                             v_date,
                             v_user.type,
                             v_start,
                             v_end);
          if v_start is not null and v_end is not null and
             v_start <= sysdate and v_end <= sysdate then
            insert into HRS_TRANSPORTED_DATA
              (select t.ID,
                      t.REC_DATE,
                      t.ACCESS_POINT_CODE,
                      t.CARD_ID,
                      t.USER_ID,
                      t.DOOR_TYPE,
                      t.cardname,
                      t.parent_id,
                      t.client_id,
                      t.time_id
                 from (select null ID,
                              v_start REC_DATE,
                              null ACCESS_POINT_CODE,
                              null CARD_ID,
                              v_user.worker USER_ID,
                              hrs_monitoring_pkg.P_DECRET_TIME_START DOOR_TYPE,
                              '' cardname,
                              v_user.parent_id,
                              v_user.client_id,
                              0 time_id
                         from dual) t
                 left join HRS_TRANSPORTED_DATA tr
                   on t.REC_DATE = tr.REC_DATE
                  and t.USER_ID = tr.USER_ID
                  and t.DOOR_TYPE > 500
                where tr.id is null
               union all
               select t.ID,
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
                              v_end REC_DATE,
                              null ACCESS_POINT_CODE,
                              null CARD_ID,
                              v_user.worker USER_ID,
                              hrs_monitoring_pkg.P_DECRET_TIME_END DOOR_TYPE,
                              '' cardname,
                              v_user.parent_id,
                              0 c,
                              0 time_id
                         from dual) t
                 left join HRS_TRANSPORTED_DATA tr
                   on t.REC_DATE = tr.REC_DATE
                  and t.USER_ID = tr.USER_ID
                  and t.DOOR_TYPE > 500
               --+ (1 / 24 / 60 * 5)
                where tr.id is null);
          end if;
          if v_start is not null and v_end is not null and
             v_start <= sysdate - (1 / 24 / 60 * 10) then
            v_id := procedures.nextval;
            insert into HRS_APPLICATIONS
              (select t.*
                 from (select v_id          id,
                              v_user.worker WORKER,
                              9             TYPE,
                              v_start       START_DATE,
                              v_end         END_DATE,
                              0             DAY_COUNT,
                              null          INFO,
                              sysdate       REC_DATE,
                              1             STATUS,
                              0             APPROVE,
                              sysdate       APPROVE_DATE,
                              0             SYNC,
                              null          SYNC_DATE,
                              0             DEL_USER,
                              null          DEL_DATE,
                              null          UCOMMENT,
                              0             auto,
                              null          files,
                              0             org,
                              null          W_HOLIDAY_COMMENT,
                              0             CLIENT_ID,
                              null          REPLACING_WORKERS,
                              0             cr,
                              0             asd,
                              0             ppid
                         from dual) t
                 left join HRS_APPLICATIONS tr
                   on t.START_DATE = tr.START_DATE
                  and t.END_DATE = tr.END_DATE
                  and t.WORKER = tr.WORKER
                where tr.id is null
               
               );
          end if;
        exception
          when others then
            return;
        end;
      end;
    end loop;
  end;
  procedure getUserDayTimesAsText(p_user_id        varchar2,
                                  p_date           varchar2,
                                  v_start          out varchar2,
                                  v_end            out varchar2,
                                  v_bstart         out varchar2,
                                  v_bend           out varchar2,
                                  hrs_user_time_id out number) is
    v_start_date   varchar2(400) := null;
    v_end_date     varchar2(400) := null;
    v_bstart_date  varchar2(400) := null;
    v_bend_date    varchar2(400) := null;
    v_start_p      date;
    v_end_p        date;
    v_bstart_p     date;
    v_bend_p       date;
    v_date         date;
    v_rest_type    number := 0;
    v_rest_minutes number := 0;
  begin
    v_end := p_date;
    -- return;
    v_date := to_date(p_date, 'yyyy-mm-dd');
    pkg_hrs_helper.getUserGraphDates(p_user_id,
                                     v_date,
                                     v_start_date,
                                     v_end_date,
                                     v_bstart_date,
                                     v_bend_date,
                                     hrs_user_time_id,
                                     v_rest_type,
                                     v_rest_minutes);
  
    if trim(v_start_date) is not null then
      begin
        select to_date(to_char(v_date, 'yyyy-mm-dd') || ' ' ||
                       trim(v_start_date),
                       'yyyy-mm-dd hh24:mi')
          into v_start_p
          from dual;
      exception
        when others then
          v_start := null;
      end;
    end if;
  
    if trim(v_end_date) is not null then
      begin
        select to_date(to_char(v_date, 'yyyy-mm-dd') || ' ' ||
                       trim(v_end_date),
                       'yyyy-mm-dd hh24:mi')
          into v_end_p
          from dual;
      exception
        when others then
          v_end := null;
      end;
    end if;
    if trim(v_bstart_date) is not null then
      begin
        select to_date(to_char(v_date, 'yyyy-mm-dd') || ' ' ||
                       v_bstart_date,
                       'yyyy-mm-dd hh24:mi')
          into v_bstart_p
          from dual;
      exception
        when others then
          v_start := null;
      end;
    end if;
  
    if trim(v_bend_date) is not null then
      begin
        select to_date(to_char(v_date, 'yyyy-mm-dd') || ' ' || v_bend_date,
                       'yyyy-mm-dd hh24:mi')
          into v_bend_p
          from dual;
      exception
        when others then
          v_end := null;
      end;
    end if;
  
    if v_start_p > v_end_p then
      v_end_p := v_end_p + 1;
    end if;
  
    if v_bstart_p > v_bend_p then
      v_bend_p := v_bend_p + 1;
    end if;
  
    v_start  := to_char(v_start_p, 'yyyy-mm-dd hh24:mi');
    v_end    := to_char(v_end_p, 'yyyy-mm-dd hh24:mi');
    v_bstart := to_char(v_bstart_p, 'yyyy-mm-dd hh24:mi');
    v_bend   := to_char(v_bend_p, 'yyyy-mm-dd hh24:mi');
  end;

  function isHolidayDayTime(p_date date, time_id number) return number is
    v_holiday number := 0;
  begin
    select count(*)
      into v_holiday
      from (select to_date(to_char(p_date, 'yyyy') || '-' || t.lib_month || '-' ||
                           t.lib_day,
                           'yyyy-mm-dd') holiday
              from LIB_HOLIDAYS t
             where t.active = 1) h
     where trunc(p_date) = h.holiday;
    if v_holiday > 0 then
      select t.holiday_yn
        into v_holiday
        from lib_graph_times t
       where t.id = time_id;
    end if;
    return v_holiday;
  end;

end pkg_hrs_helper;
