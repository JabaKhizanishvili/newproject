create or replace trigger set_user_id_trg
  before insert on hrs_transported_data
  for each row
declare
  v_type number := 0;
  v_d_type number := 0;
begin
  if :new.id is null then
    :new.id := SQS_TRANSPORTED_DATA.Nextval;
  end if;

  if :new.access_point_code is not null then
    begin
      select t.type, t.defdoor
        into v_type, v_d_type
        from LIB_DOORS t
       where lower(t.code) = lower(:new.access_point_code)
         and t.active = 1;
    exception
      WHEN OTHERS THEN
        v_type := 0;
	   v_d_type := 0;
    end;
    if (v_type = 3) then
      calculatedoorstate(:new.user_id,
                         :new.access_point_code,
                         :new.rec_date,
                         v_type);
    end if;
    :new.door_type := v_type;
  
  end if;
  if :new.user_id is not null and :new.access_point_code is not null and
     IsOfficeAllow(:new.user_id, :new.access_point_code) = 0 and
     v_d_type = 0 then
    :new.door_type := 0;
  end if;

end set_user_id_trg;
