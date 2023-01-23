create or replace trigger set_event_trg
  after insert on hrs_transported_data
  for each row
begin
  begin
    if :new.user_id > 0 and :new.door_type is not null then
      pkg_workers_monitoring.SetWorkersEvent(:new.user_id,
                                             :new.door_type,
                                             :new.rec_date,
                                             :new.time_id);
    end if;
  exception
    WHEN OTHERS THEN
      null;
  end;
end set_user_id_trg;
