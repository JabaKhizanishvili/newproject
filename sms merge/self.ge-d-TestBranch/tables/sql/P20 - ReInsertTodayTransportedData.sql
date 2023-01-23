create or replace procedure ReInsertTodayTransportedData(p_date       date := null,
                                                         p_visitor_id number := null) is
  v_count   number := 0;
  v_date    date := null;
  v_card_id varchar(400) := null;
begin
  if p_date is null then
    v_date := sysdate;
  else
    v_date := p_date;
  end if;

  if p_visitor_id is null then
    v_card_id := null;
  else
    select v.code
      into v_card_id
      from lib_visitors v
     where v.id = p_visitor_id;
  end if;

  if v_card_id is null then
    select count(1)
      into v_count
      from HRS_TRANSPORTED_DATA t
     where trunc(t.rec_date) = trunc(v_date)
       and t.user_id = 0
    -- and t.door_type in (1, 2)  
    ;
    if v_count > 0 then
      for ev in (select *
                   from HRS_TRANSPORTED_DATA t
                  where trunc(t.rec_date) = trunc(v_date)
                    and (t.user_id = 0 or t.door_type = 0)
                  order by t.rec_date asc) loop
        begin
          delete from HRS_TRANSPORTED_DATA t where t.id = ev.id;
          insert into HRS_TRANSPORTED_DATA
          values
            (ev.id,
             ev.rec_date,
             ev.access_point_code,
             ev.card_id,
             null,
             null,
             ev.cardname,
             null,
             0,
						 0
             );
          --  dbms_output.put_line(to_char(ev.rec_date, 'dd.mm.yyyy hh24:mi:ss'));
        end;
      end loop;
      commit;
    end if;
  else
    select count(1)
      into v_count
      from HRS_TRANSPORTED_DATA t
     where trunc(t.rec_date) = trunc(v_date)
       and t.user_id = 0
       and t.card_id = v_card_id
    -- and t.door_type in (1, 2)
    ;
    if v_count > 0 then
      for ev in (select *
                   from HRS_TRANSPORTED_DATA t
                  where trunc(t.rec_date) = trunc(v_date)
                    and (t.user_id = 0 or t.door_type = 0)
                    and t.card_id = v_card_id
                  order by t.rec_date asc) loop
        begin
          delete from HRS_TRANSPORTED_DATA t where t.id = ev.id;
          insert into HRS_TRANSPORTED_DATA
          values
            (ev.id,
             ev.rec_date,
             ev.access_point_code,
             ev.card_id,
             null,
             null,
             ev.cardname,
             null,
             0,
						 0
             );
          --  dbms_output.put_line(to_char(ev.rec_date, 'dd.mm.yyyy hh24:mi:ss'));
        end;
      end loop;
      commit;
    end if;
  end if;
end ReInsertTodayTransportedData;
