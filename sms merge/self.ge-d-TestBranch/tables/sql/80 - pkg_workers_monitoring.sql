create or replace package pkg_workers_monitoring is

  -- Author  : TEIMURAZ.KEVLISHVILI
  -- Created : 24.01.2017 3:08:56
  -- Purpose :
  -- Declare variables
  P_DAY_OFF_ID        number := 1500;
  P_START_DAY_ID      number := 2000;
  P_START_BREAK_ID    number := 2500;
  P_END_BREAK_ID      number := 3000;
  P_END_DAY_ID        number := 3500;
  P_DECRET_TIME_START number := 4000;
  P_DECRET_TIME_END   number := 4500;

  P_DAY_OFF_TEXT varchar2(4000);

  procedure SetWorkersEvent(p_user_id   in number,
                            p_door_type in number,
                            p_date      in date,
                            time_id in number default 0);

  function CalculateLateness(p_start_date date, p_end_date date)
    return number;

  procedure SetVars;

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
                        EventType           in number default 0);

  procedure SetDayStartTime(p_worker number, p_date date);

  procedure SetDayEndTime(p_worker number, pp_date date);

  function PrivateTimeIsEnabled(p_staff number) return number;

  function GetOrgPersonID(p_staff number) return number;

  function GetUsedAutoPrivateTime(p_staff number) return number;

end pkg_workers_monitoring;
