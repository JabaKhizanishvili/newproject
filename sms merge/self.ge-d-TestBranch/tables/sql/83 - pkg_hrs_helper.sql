create or replace package pkg_hrs_helper is

  procedure getUserDayTimes(p_user_id        number,
                            p_date           date,
                            v_start          out date,
                            v_end            out date,
                            v_bstart         out date,
                            v_bend           out date,
                            hrs_user_time_id out number,
                            v_rest_type      out number,
                            v_rest_minutes   out number
                            --,
                            -- hrs_user_graph_type out number:=0
                            );
                            
  procedure getUserDayTimesNew(p_user_id        number,
                               p_date           date,
                               v_start          out date,
                               v_end            out date,
                               v_bstart         out date,
                               v_bend           out date,
                               hrs_user_time_id out number,
                               v_rest_type      out number,
                               v_rest_minutes   out number,
                               timecontrol      out number);

  procedure getUserGraphDates(p_user_id          in number,
                              p_date             in date,
                              v_start_date       out varchar2,
                              v_end_date         out varchar2,
                              v_bstart_date      out varchar2,
                              v_bend_date        out varchar2,
                              user_graph_time_id out number,
                              v_rest_type        out number,
                              v_rest_minutes     out number);

  function getUserTimeIDByGraph(p_graph_type number, p_date date)
    return number;

  function getUserTimeIDByDinGraph(p_user_id number, p_date date)
    return number;

  procedure getUsedRestMinutes(p_start_date        date,
                               p_end_date          date,
                               p_worker            number,
                               v_rest_minutes      number,
                               TrueLatenessMinutes out number,
                               EventType           out number,
                               LatenessMinutes     in out number,
                               LatenessReason      in out varchar2);
  function isHoliday(p_date date) return number;

  function isTomorrowUserHoliday(p_worker number, p_date date) return number;

  function getWorkerLastOUTDate(v_stuff_id number, p_date date) return date;

  function getWorkerLastINDate(v_stuff_id number, p_date date) return date;

  function getWorkerLastInVirtDate(v_stuff_id number, p_date date)
    return date;

  function getWorkerLastOutVirtDate(v_stuff_id number, p_date date)
    return date;

  function getOutDoorsIDx return varchar2;

  function getOutVirtIDx return varchar2;

  function getInDoorsIDx return varchar2;

  function getInVirtIDx return varchar2;

  function getLimitedOutTypes return varchar2;

  Procedure StartPositiveReason(p_staf_id    number,
                                p_Start_Date Date,
                                p_day_end    date);

  Procedure ENDPositiveReason(p_staf_id number, p_Start_Date Date);

  procedure getPositiveReason(p_user_id     in number,
                              p_date        in date,
                              v_result      out number,
                              v_reason      out varchar2,
                              v_more_reason out varchar2,
                              v_type_name   out varchar2,
                              v_type        out number);

  procedure stopBulletin(p_worker number, p_date date);

  function getWorkerLastEventType(p_worker number, p_date date) return number;

  function getWorkerLastEventDate(p_worker number, p_date date) return date;

  function HaveWorkerStandardGraph(p_worker number) return number;

  function UserHasDecretTime(p_worker number, p_date date) return number;

  procedure getUserDecretTimes(p_user_id number,
                               p_date    date,
                               p_type    number,
                               v_start   out date,
                               v_end     out date);
  procedure InsertWorkersDecretTime(p_date date := null);

  procedure getUserDayTimesAsText(p_user_id        varchar2,
                                  p_date           varchar2,
                                  v_start          out varchar2,
                                  v_end            out varchar2,
                                  v_bstart         out varchar2,
                                  v_bend           out varchar2,
                                  hrs_user_time_id out number);

 function isHolidayDayTime(p_date date, time_id number) return number;

end pkg_hrs_helper;
