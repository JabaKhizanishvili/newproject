create or replace function GetNextDate(user_id    number,
                                       Start_date date,
                                       vtype      number) return date is
  Result date;
begin
  select m.event_date
    into Result
    from (select ee.event_date,
                 row_number() over(partition by ee.staff_id order by ee.event_date asc) rn
            from HRS_STAFF_EVENTS ee
           where ee.staff_id = user_id
             and ee.event_date > Start_date
             and ee.real_type_id = vtype) m
   where m.rn = 1;
  return(Result);
end GetNextDate;