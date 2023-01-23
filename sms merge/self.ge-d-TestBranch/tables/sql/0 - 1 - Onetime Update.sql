DECLARE
  v_exists varchar2(300);
begin
  begin
    SELECT a.VIEW_NAME
      INTO v_exists
      FROM all_views a
     where lower(a.VIEW_NAME) = 'rel_worker_chief';
  exception
    when others then
      return;
  end;
  execute immediate ' DROP VIEW REL_WORKER_CHIEF ';
  execute immediate ' CREATE table REL_WORKER_CHIEF AS SELECT * FROM V_REL_WORKER_CHIEF ';
  execute immediate ' create index REL_WORKER_CHIEF_IDX_1 on REL_WORKER_CHIEF (WORKER_PID) ';
  execute immediate ' create index REL_WORKER_CHIEF_IDX_2 on REL_WORKER_CHIEF (WORKER_OPID) ';
  execute immediate ' create index REL_WORKER_CHIEF_IDX_3 on REL_WORKER_CHIEF (WORKER) ';
  execute immediate ' create index REL_WORKER_CHIEF_IDX_4 on REL_WORKER_CHIEF (ORG) ';
  execute immediate ' create index REL_WORKER_CHIEF_IDX_5 on REL_WORKER_CHIEF (CHIEF_PID) ';
  execute immediate ' create index REL_WORKER_CHIEF_IDX_6 on REL_WORKER_CHIEF (CHIEF_OPID) ';
  execute immediate ' create index REL_WORKER_CHIEF_IDX_7 on REL_WORKER_CHIEF (CHIEF) ';
  execute immediate ' create index REL_WORKER_CHIEF_IDX_8 on REL_WORKER_CHIEF (CLEVEL) ';

end;
