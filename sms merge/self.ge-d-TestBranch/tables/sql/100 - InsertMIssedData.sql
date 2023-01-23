create or replace procedure InsertMIssedData is
  v_count number := 0;
begin
  for c in (select null         ID,
                   k.ActionDate REC_DATE,
                   null         ACCESS_POINT_CODE,
                   null         CARD_ID,
                   k.id         USER_ID,
                   k.GTYPE      DOOR_TYPE,
                   null         CARDNAME,
                   k.PARENT_ID  parent_id
              from (select u.id,
                           u.real_date,
                           CASE
                             WHEN gt.GTYPE = 2000 then
                              to_date(to_char(u.real_date, 'yyyy-mm-dd') || ' ' ||
                                      gt.GDATE,
                                      'yyyy-mm-dd hh24:mi')
                             WHEN replace(gt.GDATE, ':', '') <=
                                  replace(gt.GSTART, ':', '') then
                              to_date(to_char(u.real_date + 1, 'yyyy-mm-dd') || ' ' ||
                                      gt.gdate,
                                      'yyyy-mm-dd hh24:mi')
                             else
                              to_date(to_char(u.real_date, 'yyyy-mm-dd') || ' ' ||
                                      gt.gdate,
                                      'yyyy-mm-dd hh24:mi')
                           end ActionDate,
                           gt.GTYPE,
                           u.parent_id
                      from (select w.id, g.real_date, g.time_id, w.PARENT_ID
                              from HRS_WORKERS_SCH w
                              left join hrs_graph g
                                on g.worker = w.id
                             where w.graphtype = 0
                               and w.active > 0
                               and g.real_date between trunc(sysdate) - 1 and
                                   sysdate
                            union all
                            select w.id,
                                   r.mdate,
                                   case
                                     when trim(to_char(r.mdate, 'DAY')) =
                                          'MONDAY' then
                                      s.MONDAY
                                     when trim(to_char(r.mdate, 'DAY')) =
                                          'TUESDAY' then
                                      s.TUESDAY
                                     when trim(to_char(r.mdate, 'DAY')) =
                                          'WEDNESDAY' then
                                      s.WEDNESDAY
                                     when trim(to_char(r.mdate, 'DAY')) =
                                          'THURSDAY' then
                                      s.THURSDAY
                                     when trim(to_char(r.mdate, 'DAY')) =
                                          'FRIDAY' then
                                      s.FRIDAY
                                     when trim(to_char(r.mdate, 'DAY')) =
                                          'SATURDAY' then
                                      s.SATURDAY
                                     when trim(to_char(r.mdate, 'DAY')) =
                                          'SUNDAY' then
                                      s.SUNDAY
                                   end AS time_id,
                                   w.PARENT_ID
                              from HRS_WORKERS_SCH w
                              left join LIB_STANDARD_GRAPHS s
                                on s.id = w.graphtype
                              LEFT JOIN (select trunc(sysdate) - 1 mdate
                                           from dual
                                         union all
                                         select trunc(sysdate) mdate from dual) r
                                on 1 = 1
                             where w.graphtype > 0
                               and w.active > 0) u
                      left join hrs_v_graph_times_list gt
                        on gt.ID = u.time_id) k
              left join hrs_transported_data tr
                on tr.rec_date > sysdate - 4
               and tr.rec_date = k.ActionDate
               and tr.user_id = k.id
               and tr.door_type = k.GTYPE
             where ((k.GTYPE = 2000 and
                   k.ActionDate <= sysdate + 1 / 24 / 60 * 30) or
                   (k.GTYPE > 2000 and k.ActionDate <= sysdate))
               and tr.id is null
             order by k.ActionDate asc, k.GTYPE asc) loop
    begin
      v_count := v_count + 1;
      insert into HRS_TRANSPORTED_DATA
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
                       c.rec_date REC_DATE,
                       c.access_point_code,
                       c.CARD_ID,
                       c.USER_ID,
                       c.door_type,
                       '' cardname,
                       c.parent_id,
                       0 c,
                       0 time_id
                  from dual) t;
      commit;
    end;
  end loop;
end InsertMIssedData;
