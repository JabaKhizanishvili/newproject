create or replace package hrs_monitoring_pkg is

  -- Author  : TEIMURAZ.KEVLISHVILI
  -- Created : 13.07.2016 9:36:08
  -- Purpose :

  -- Public function and procedure declarations
  p_back_time number := 1 / 24 / 5;

  P_DAY_OFF_ID        number := 1500;
  P_START_DAY_ID      number := 2000;
  P_START_BREAK_ID    number := 2500;
  P_END_BREAK_ID      number := 3000;
  P_END_DAY_ID        number := 3500;
  P_DECRET_TIME_START number := 4000;
  P_DECRET_TIME_END   number := 4500;

  procedure StartFixedMonitoring;

  procedure SetStartTime;

  procedure SetEndTime;

  procedure SetBreakStart;

  procedure SetBreakEnd;

  function getBackTime return number;

  function isTodayHoliday return number;

  function getLastOutTime(p_user_id number, p_date date) return date;

  function CalculateLateness(p_user_id number, p_date date) return number;
	
	procedure update_rel_worker_chief;

end hrs_monitoring_pkg;
