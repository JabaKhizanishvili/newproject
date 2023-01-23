create or replace procedure CalculateDoorState(v_user   in number,
                                               p_DoorID in varchar2,
                                               p_Date   in date,
                                               p_dtype  out number) is
  v_doot_type      number := 0;
  v_rec_date       date;
  v_rem            number;
  v_prev_door_type number := 0;
begin
  p_dtype := 0;
  begin
    select d.type
      into v_doot_type
      from lib_doors d
     where lower(d.code) = lower(p_DoorID)
       and d.active = 1;
    if (v_doot_type = 3) then
      begin
        select l.rec_date, l.door_type
          into v_rec_date, v_prev_door_type
          from (select k.*, rownum as rn
                  from (select t.rec_date, t.door_type
                          from hrs_transported_data t
                         where t.user_id = v_user
                           and t.rec_date <= p_Date
                           and t.door_type < 100
                           and t.door_type > 0
                         order by t. rec_date desc) k) l
         where l.rn = 1;
        v_rem := (p_Date - v_rec_date) * 24 * 60;
        if (v_rem >= 5) then
          if (v_prev_door_type = 1) then
            p_dtype := 2;
          else
            p_dtype := 1;
          end if;
          return;
        end if;
      exception
        when others then
          p_dtype := 1;
      end;
    end if;
  exception
    when others then
      p_dtype := 0;
  end;
end CalculateDoorState;
