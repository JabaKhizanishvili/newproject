create or replace package body pkg_workers_monitoring is
  procedure SetWorkersEvent(p_user_id   in number,
                            p_door_type in number,
                            p_date      in date,
                            time_id in number default 0) is
    LastOutDate date := null;
    /* LastInDate  date := null;*/
    LastEventDate         date := null;
    LastEventType         number := null;
    v_rest_type           number := 0;
    v_rest_minutes        number := 0;
    LastInVirtDate        date := null;
    hrs_user_day_start    date := null;
    hrs_user_day_end      date := null;
    hrs_user_breack_start date := null;
    hrs_user_breack_end   date := null;
    hrs_user_time_id      number := 0;
    /*   hrs_user_decret_hour_start date := null;
    hrs_user_decret_hour_end   date := null;
     hrs_user_has_decret        number := 0;*/
    StartLateness date := null;
    EndLateness   date := null;
    -- LatenessMinutesMin     number := 0;
    LatenessMinutesMax     number := 0;
    AutoPrivateTime        number := 0;
    PrivateTimeSum         number := 0;
    UserPrivateTime        number := 0;
    LatenessMinutes        number := 0;
    LatenessReason         varchar2(4000) := null;
    LatenessAppReason      varchar2(4000) := null;
    LatenessMoreReason     varchar2(4000) := null;
    LatenessReasonType     number := null;
    v_reason               varchar2(4000) := null;
    LatenessReasonTypeName varchar2(4000) := null;
    TrueLatenessMinutes    number := 0;
    --  UsedRestMinutes        number := 0;
    /**
    EventType-s:
    1 - Morning Lateness
    2 - less than LatenessMinutesMax
    3 - RestTime
    4 - Lateness
    5 - Double In/Out
    */
    EventType         number := 0;
    AppID             number := 0;
    p_worker          number := 0;
    timecontrol       number := 0;
    v_duplicate_times number := 0;
    --P_Holiday_before_minutes number := 0;
  begin
    --  P_Holiday_before_minutes := getconfig('hr_holiday_before_minutes');
    LatenessMinutesMax := getconfig('hr_excuse_minutes');
    AutoPrivateTime    := getconfig('private_date_auto_approved');
    UserPrivateTime    := pkg_workers_monitoring.PrivateTimeIsEnabled(p_user_id);
    v_duplicate_times  := getconfig('duplicate_in_out');
    pkg_hrs_helper.getuserdaytimesnew(p_user_id,
                                      p_date,
                                      hrs_user_day_start,
                                      hrs_user_day_end,
                                      hrs_user_breack_start,
                                      hrs_user_breack_end,
                                      hrs_user_time_id,
                                      v_rest_type,
                                      v_rest_minutes,
                                      timecontrol);
  
    if p_door_type > 100 and hrs_user_time_id > 0 and
       hrs_user_day_start is not null and
       pkg_hrs_helper.isHolidayDayTime(hrs_user_day_start, hrs_user_time_id) > 0 then
      return;
    end if;
  
    p_worker := pkg_workers_monitoring.GetOrgPersonID(p_user_id);
    -- /* Set REC_date if User has Positive Reason
    begin
      if (p_door_type in (2)) then
        pkg_hrs_helper.StartPositiveReason(p_worker,
                                           p_date,
                                           hrs_user_day_end);
      end if;
    end;
    -- /* Set End_date if User has Positive Reason
    begin
      if (p_door_type in (1, pkg_workers_monitoring.P_DECRET_TIME_START)) then
        pkg_hrs_helper.ENDPositiveReason(p_worker, p_date);
        pkg_hrs_helper.stopBulletin(p_worker, p_date);
      end if;
    end;
  
    LastEventType := pkg_hrs_helper.getWorkerLastEventType(p_user_id,
                                                           p_date);
    LastEventDate := pkg_hrs_helper.getWorkerLastEventdate(p_user_id,
                                                           p_date);
    LastOutDate   := pkg_hrs_helper.getWorkerLastOUTDate(p_user_id, p_date);
    /* LastInDate     := pkg_hrs_helper.getWorkerLastINDate(p_user_id, p_date);*/
    LastInVirtDate := pkg_hrs_helper.getWorkerLastInVirtDate(p_user_id,
                                                             p_date);
    /*    dbms_output.put_line('hrs_user_breack_start');
    dbms_output.put_line(to_char(hrs_user_day_start,
                                 'yyyy-mm-dd hh24:mi:ss'));
    dbms_output.put_line(to_char(hrs_user_day_end, 'yyyy-mm-dd hh24:mi:ss'));
    dbms_output.put_line('p_door_type');
    dbms_output.put_line(p_door_type);*/
    if (p_door_type in (1,
                        pkg_workers_monitoring.P_START_BREAK_ID,
                        pkg_workers_monitoring.P_END_BREAK_ID,
                        pkg_workers_monitoring.P_END_DAY_ID,
                        pkg_workers_monitoring.P_DECRET_TIME_END,
                        pkg_workers_monitoring.P_DECRET_TIME_START) and
       LastEventType = 2 and p_date between hrs_user_day_start and
       hrs_user_day_end) then
    
      -- /* get Lateness start date
      select max(v_date)
        into StartLateness
        from (select LastOutDate v_date
                from dual
              union all
              select LastInVirtDate v_date
                from dual
              union all
              select hrs_user_day_start v_date
                from dual
              union all
              select case
                       when hrs_user_breack_start <> p_date then
                        hrs_user_breack_start
                       else
                        null
                     end v_date
                from dual
              union all
              select case
                       when hrs_user_breack_end <> p_date then
                        hrs_user_breack_end
                       else
                        null
                     end v_date
                from dual
              /*              union all
              select hrs_user_breack_end v_date from dual*/
              
              )
       where v_date <= p_date;
    
      if StartLateness < p_date then
        LatenessMinutes := CalculateLateness(StartLateness, p_date);
      end if;
    
    end if;
    /*    dbms_output.put_line(LastEventType);
    dbms_output.put_line(StartLateness);
    dbms_output.put_line(LatenessMinutes);*/
    if LatenessMinutes > 0 then
      pkg_hrs_helper.getpositivereason(p_worker,
                                       p_date,
                                       AppID,
                                       LatenessAppReason,
                                       LatenessMoreReason,
                                       LatenessReasonTypeName,
                                       LatenessReasonType);
      --      dbms_output.put_line(p_worker);
      --      dbms_output.put_line(to_char(p_date, 'yyyy-mm-dd hh24:mi:ss'));
      if AppID > 0 then
        LatenessReason := trim(LatenessReasonTypeName || ' ' ||
                               LatenessAppReason || ' (' ||
                               LatenessMoreReason || ')');
      
      ELSE
        LatenessReason := '?';
      end if;
    
      if StartLateness = hrs_user_day_start and LatenessMinutes > 0 then
        EventType := 1;
      end if;
    
      if AppID = 0 and LatenessMinutes <= LatenessMinutesMax and
         StartLateness <> hrs_user_day_start and p_date <> hrs_user_day_end and
         p_date <> hrs_user_breack_start then
        TrueLatenessMinutes := LatenessMinutes;
        EventType           := 2;
        LatenessReason      := null;
        LatenessMinutes     := 0;
      end if;
    
      if AppID > 0 and LatenessReasonType <> 2 and
         p_door_type <> pkg_workers_monitoring.P_DECRET_TIME_START then
        LatenessMinutes := 0;
      end if;
    
      if AppID > 0 and LatenessReasonType <> 2 and
         p_door_type = pkg_workers_monitoring.P_DECRET_TIME_START then
        LatenessReason := '?';
      end if;
    
      if p_date > hrs_user_breack_start and p_date <= hrs_user_breack_end and
         LatenessMinutes > 0 then
        if v_rest_type <> 4 then
          LatenessMinutes := 0;
          LatenessReason  := null;
        else
          pkg_hrs_helper.getUsedRestMinutes(hrs_user_breack_start,
                                            hrs_user_breack_end,
                                            p_user_id,
                                            v_rest_minutes,
                                            TrueLatenessMinutes,
                                            EventType,
                                            LatenessMinutes,
                                            LatenessReason);
        end if;
      end if;
    
      if LatenessMinutes <= 0 AND LatenessReason = '?' then
        LatenessMinutes := 0;
        LatenessReason  := null;
      end if;
    end if;
  
    --- Double Event Trigger
    if LastEventDate < hrs_user_day_start then
      LastEventDate := hrs_user_day_start;
    end if;
    if LastEventDate >= hrs_user_day_end then
      LastEventDate := null;
    end if;
  
    if p_date <= hrs_user_day_start then
      LastEventDate := null;
    end if;
  
    if p_date >= hrs_user_day_end then
      EndLateness := hrs_user_day_end;
    end if;
    if EndLateness is null then
      EndLateness := p_date;
    end if;
    if LastEventType = p_door_type and LastEventDate is not null and
       LastEventDate < (p_date - 1 / 24 / 60 * 5) and
       v_duplicate_times = '1' then
      LatenessMinutes := CalculateLateness(LastEventDate, EndLateness);
      select t.lib_title
        into v_reason
        from LIB_APPLICATIONS_TYPES t
       where t.type = 12;
    
      LatenessReason := v_reason || ' (  )';
      EventType      := 5;
    end if;
  
    if timecontrol = 0 then
      LatenessMinutes := 0;
      LatenessReason  := null;
    end if;
  
    if timecontrol = 0 then
      LatenessMinutes := 0;
      LatenessReason  := null;
    end if;
  
    if hrs_user_time_id > 0 and hrs_user_day_start is not null and
       pkg_hrs_helper.isHolidayDayTime(hrs_user_day_start, hrs_user_time_id) > 0 then
      LatenessMinutes := 0;
      if LatenessReason = '?' then
        LatenessReason := null;
      end if;
    end if;
  
    if AutoPrivateTime = 1 and UserPrivateTime = 1 and LatenessMinutes > 0 and
       AppID <= 0 then
      AppID := -555;
      select t.lib_title
        into LatenessReasonTypeName
        from LIB_APPLICATIONS_TYPES t
       where t.type = 2;
      LatenessReason := trim(LatenessReasonTypeName || ' ' || ' ( )');
    end if;
  
    if AutoPrivateTime = 2 and UserPrivateTime = 1 and LatenessMinutes > 0 and
       AppID <= 0 then
      select t.lib_title
        into LatenessReasonTypeName
        from LIB_APPLICATIONS_TYPES t
       where t.type = 2;
      PrivateTimeSum := pkg_workers_monitoring.GetUsedAutoPrivateTime(p_worker);
      if (PrivateTimeSum - LatenessMinutes) >= 0 then
        LatenessReason := trim(LatenessReasonTypeName || ' ' || ' ( )');
        AppID          := -555;
      elsif (LatenessMinutes - PrivateTimeSum) < LatenessMinutes then
        LatenessReason  := trim(LatenessReasonTypeName || ' ' || ' ( ' ||
                                PrivateTimeSum || ' )');
        LatenessMinutes := LatenessMinutes - PrivateTimeSum;
        AppID           := -555;
      end if;
    end if;

		if hrs_user_time_id is null then
			hrs_user_time_id := time_id;
    end if;
			
    --/*Insert User Event Data*/
    pkg_workers_monitoring.InsertEvent(p_user_id,
                                       p_door_type,
                                       p_date,
                                       LatenessMinutes,
                                       LatenessReason,
                                       AppID,
                                       hrs_user_time_id,
                                       to_number(to_char(p_date, 'DDD')),
                                       to_number(to_char(p_date, 'yyyy')),
                                       LastOutDate,
                                       LastEventType,
                                       TrueLatenessMinutes,
                                       EventType);
    /*Return*/
    return;
  end;

  function CalculateLateness(p_start_date date, p_end_date date)
    return number is
    v_diff    number := 0;
    v_minutes number := 0;
  begin
    v_diff := trunc(ROUND(((p_end_date - p_start_date) * 60 * 24), 2), 0);
    if (v_diff > 0) then
      v_minutes := v_diff;
    end if;
    return v_minutes;
  end;

  procedure SetVars is
  begin
    if pkg_hrs_monitoring.P_DAY_OFF_TEXT is null then
      select a.lib_title
        into pkg_hrs_monitoring.P_DAY_OFF_TEXT
        from lib_actions a
       where a.type = pkg_hrs_monitoring.P_DAY_OFF_ID;
    end if;
  end;

  procedure InsertEvent(p_worker            in number,
                        p_code              in number,
                        p_date              in date,
                        v_time_min          in number,
                        p_coment            in varchar2,
                        p_app_id            in number,
                        p_time_id           in number,
                        p_gt_day            in number,
                        p_gt_year           in number,
                        LastOutDate         in date,
                        LastEventType       in number,
                        TrueLatenessMinutes in number default 0,
                        EventType           in number default 0
                        
                        ) is
    timeMin number := 0;
  begin
    if TrueLatenessMinutes = 0 and v_time_min <> 0 then
      timeMin := v_time_min;
    else
      timeMin := TrueLatenessMinutes;
    end if;
  
    insert into HRS_STAFF_EVENTS
      (ID,
       STAFF_ID,
       REAL_TYPE_ID,
       EVENT_DATE,
       TIME_COMMENT,
       TIME_ID,
       GT_DAY,
       GT_YEAR,
       TIME_MIN,
       APP_ID,
       PREV_EVENT_DATE,
       PREV_EVENT_TYPE,
       TRUE_TIME_MIN,
       EVENT_TYPE)
    values
      (events_sqs.nextval,
       p_worker,
       p_code,
       p_date,
       p_coment,
       p_time_id,
       p_gt_day,
       p_gt_year,
       v_time_min,
       p_app_id,
       LastOutDate,
       LastEventType,
       timeMin,
       EventType);
  end;

  procedure SetDayStartTime(p_worker number, p_date date) is
    hrs_user_day_start    date := null;
    hrs_user_day_end      date := null;
    hrs_user_breack_start date := null;
    hrs_user_breack_end   date := null;
    hrs_user_time_id      number := 0;
    v_rest_type           number := 0;
    v_rest_minutes        number := 0;
  begin
    pkg_hrs_monitoring.SetVars;
    pkg_hrs_helper.getUserDayTimes(p_worker,
                                   p_date,
                                   hrs_user_day_start,
                                   hrs_user_day_end,
                                   hrs_user_breack_start,
                                   hrs_user_breack_end,
                                   hrs_user_time_id,
                                   v_rest_type,
                                   v_rest_minutes);
    begin
      --   dbms_output.put_line(hrs_user_time_id);
      if hrs_user_time_id > 0 then
      
        pkg_hrs_monitoring.InsertEvent(p_worker,
                                       pkg_hrs_monitoring.P_START_DAY_ID,
                                       hrs_user_day_start,
                                       0,
                                       null,
                                       0,
                                       hrs_user_time_id,
                                       to_number(to_char(p_date, 'DDD')),
                                       to_number(to_char(p_date, 'yyyy')));
      else
        --     dbms_output.put_line(hrs_user_time_id);
        pkg_hrs_monitoring.InsertEvent(p_worker,
                                       pkg_hrs_monitoring.P_DAY_OFF_ID,
                                       p_date,
                                       0,
                                       pkg_hrs_monitoring.P_DAY_OFF_TEXT,
                                       0,
                                       hrs_user_time_id,
                                       to_number(to_char(p_date, 'DDD')),
                                       to_number(to_char(p_date, 'yyyy')));
      end if;
    
    end;
  end;

  procedure SetDayEndTime(p_worker number, pp_date date) is
    hrs_user_day_start     date := null;
    hrs_user_day_end       date := null;
    hrs_user_breack_start  date := null;
    hrs_user_breack_end    date := null;
    hrs_user_time_id       number := 0;
    LatenessMinutes        number := 0;
    AppID                  number := 0;
    LastOutDate            date := null;
    LastInVirtDate         date := null;
    StartLateness          date := null;
    LatenessReasonType     number := null;
    LatenessReason         varchar2(4000) := null;
    LatenessAppReason      varchar2(4000) := null;
    LatenessMoreReason     varchar2(4000) := null;
    LatenessReasonTypeName varchar2(4000) := null;
    p_date                 date;
    v_rest_type            number := 0;
    v_rest_minutes         number := 0;
  begin
    if pp_date > sysdate then
      p_date := sysdate;
    else
      p_date := pp_date;
    end if;
    pkg_hrs_monitoring.SetVars;
    pkg_hrs_helper.getUserDayTimes(p_worker,
                                   p_date,
                                   hrs_user_day_start,
                                   hrs_user_day_end,
                                   hrs_user_breack_start,
                                   hrs_user_breack_end,
                                   hrs_user_time_id,
                                   v_rest_type,
                                   v_rest_minutes);
    begin
      if hrs_user_time_id > 0 then
        --/*??????????? ?????????*/
        pkg_hrs_helper.ENDPositiveReason(p_worker, hrs_user_day_end);
      
        --/* ???? ??????? ?? ???? ????????? ??????*/
        LastOutDate    := pkg_hrs_helper.getWorkerLastOUTDate(p_worker,
                                                              hrs_user_day_start);
        LastInVirtDate := pkg_hrs_helper.getWorkerLastInVirtDate(p_worker,
                                                                 hrs_user_day_end);
        --   LastInDate     := pkg_hrs_helper.getWorkerLastINDate(p_worker,
        --                                                     hrs_user_day_end);
        if (hrs_user_day_end between hrs_user_day_start and
           hrs_user_day_end) then
          -- /* get Lateness start date
          select max(v_date)
            into StartLateness
            from (select LastOutDate v_date
                    from dual
                  union all
                  select LastInVirtDate v_date
                    from dual
                  union all
                  select hrs_user_day_start v_date
                    from dual
                  union all
                  select hrs_user_breack_end v_date from dual)
           where v_date <= hrs_user_day_end;
        
          if StartLateness < hrs_user_day_end then
            LatenessMinutes := CalculateLateness(StartLateness,
                                                 hrs_user_day_end);
          end if;
        end if;
      
        pkg_hrs_helper.getpositivereason(p_worker,
                                         hrs_user_day_end,
                                         AppID,
                                         LatenessAppReason,
                                         LatenessMoreReason,
                                         LatenessReasonTypeName,
                                         LatenessReasonType);
      
        if AppID > 0 then
          LatenessReason := trim(LatenessReasonTypeName || ' ' ||
                                 LatenessAppReason || ' (' ||
                                 LatenessMoreReason || ')');
        
        ELSE
          LatenessReason := '?';
        end if;
        pkg_hrs_monitoring.InsertEvent(p_worker,
                                       pkg_hrs_monitoring.P_END_DAY_ID,
                                       hrs_user_day_end,
                                       LatenessMinutes,
                                       LatenessReason,
                                       AppID,
                                       hrs_user_time_id,
                                       to_number(to_char(hrs_user_day_end,
                                                         'DDD')),
                                       to_number(to_char(hrs_user_day_end,
                                                         'yyyy')));
      else
        pkg_hrs_monitoring.InsertEvent(p_worker,
                                       pkg_hrs_monitoring.P_DAY_OFF_ID,
                                       hrs_user_day_end,
                                       0,
                                       pkg_hrs_monitoring.P_DAY_OFF_TEXT,
                                       0,
                                       hrs_user_time_id,
                                       to_number(to_char(hrs_user_day_end,
                                                         'DDD')),
                                       to_number(to_char(hrs_user_day_end,
                                                         'yyyy')));
      end if;
    
    end;
  end;

  function PrivateTimeIsEnabled(p_staff number) return number is
    v_person_org number := 0;
  begin
    begin
      if getconfig('private_date') = 0 then
        return 0;
      end if;
      select trim(COLUMN_VALUE) text
        into v_person_org
        from xmltable(replace(getconfig('private_date_orgs'), '|', ','))
       where trim(COLUMN_VALUE) in
             (select p.org from slf_worker p where p.id = p_staff);
    exception
      when others then
        return 0;
    end;
    if v_person_org > 0 then
      return 1;
    else
      return 0;
    end if;
  end;

  function GetOrgPersonID(p_staff number) return number is
    v_person number := 0;
  begin
    begin
      select p.orgpid into v_person from slf_worker p where p.id = p_staff;
    exception
      when others then
        return v_person;
    end;
    return v_person;
  end;

  function GetUsedAutoPrivateTime(p_staff number) return number is
    v_config  number := 0;
    v_Start   date;
    v_End     date;
    v_minutes number := 0;
  begin
    begin
      v_config := getconfig('paramsprivate_date_period');
      if v_config < 1 then
        v_config := 1;
      end if;
    
      if v_config = 1 then
        select trunc(last_day(sysdate)) into v_Start from dual;
        select trunc((sysdate), 'month') into v_End from dual;
      elsif v_config = 2 then
        SELECT CASE to_number(to_char(sysdate, 'mm'))
                 WHEN 1 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-01-01', 'yyyy-mm-dd')
                 WHEN 2 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-01-01', 'yyyy-mm-dd')
                 WHEN 3 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-01-01', 'yyyy-mm-dd')
                 WHEN 4 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-04-01', 'yyyy-mm-dd')
                 WHEN 5 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-04-01', 'yyyy-mm-dd')
                 WHEN 6 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-04-01', 'yyyy-mm-dd')
                 WHEN 7 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-07-01', 'yyyy-mm-dd')
                 WHEN 8 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-07-01', 'yyyy-mm-dd')
                 WHEN 9 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-07-01', 'yyyy-mm-dd')
                 WHEN 10 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-10-01', 'yyyy-mm-dd')
                 WHEN 11 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-10-01', 'yyyy-mm-dd')
                 WHEN 12 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-10-01', 'yyyy-mm-dd')
               END
          into v_Start
          FROM dual;
      
        SELECT CASE to_number(to_char(sysdate, 'mm'))
                 WHEN 1 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-03-31', 'yyyy-mm-dd')
                 WHEN 2 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-03-31', 'yyyy-mm-dd')
                 WHEN 3 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-03-31', 'yyyy-mm-dd')
                 WHEN 4 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-06-30', 'yyyy-mm-dd')
                 WHEN 5 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-06-30', 'yyyy-mm-dd')
                 WHEN 6 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-06-30', 'yyyy-mm-dd')
                 WHEN 7 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-09-30', 'yyyy-mm-dd')
                 WHEN 8 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-09-30', 'yyyy-mm-dd')
                 WHEN 9 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-09-30', 'yyyy-mm-dd')
                 WHEN 10 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-12-31', 'yyyy-mm-dd')
                 WHEN 11 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-12-31', 'yyyy-mm-dd')
                 WHEN 12 THEN
                  to_date(to_char(sysdate, 'yyyy') || '-12-31', 'yyyy-mm-dd')
               END
          into v_End
          FROM dual;
      
      elsif v_config = 3 then
        SELECT to_date(to_char(sysdate, 'yyyy') || '-01-01', 'yyyy-mm-dd')
          into v_Start
          FROM dual;
      
        SELECT to_date(to_char(sysdate, 'yyyy') || '-12-31', 'yyyy-mm-dd')
          into v_End
          FROM dual;
      end if;
    
      SELECT nvl(to_number(getconfig('private_date_limit')) -
                 sum(hse.TIME_MIN),
                 0)
        into v_minutes
        FROM HRS_STAFF_EVENTS hse
       WHERE hse.STAFF_ID = p_staff
         AND hse.APP_ID IN (-555, 2)
         AND hse.C_RESOLUTION = 0
         AND trunc(hse.EVENT_DATE) BETWEEN v_Start AND v_End;
    exception
      when others then
        return v_minutes;
    end;
    return v_minutes;
  end;
end pkg_workers_monitoring;
