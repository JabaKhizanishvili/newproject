create or replace function getchiefsbyworker(p_worker number)
  return varchar2 is
  v_result varchar2(4000) := ' ';
begin
  for cur in (select w.firstname || ' ' || w.lastname all_chiefs
                from rel_worker_chief wc
                left join hrs_workers w
                  on w.id = wc.chief
               where wc.worker = p_worker) loop
    begin
      if (v_result = ' ') then
        v_result := cur.all_chiefs;
      else
        v_result := v_result || ', ' || cur.all_chiefs;
      end if;
    end;
  end loop;
  return(v_result);
end getchiefsbyworker;
