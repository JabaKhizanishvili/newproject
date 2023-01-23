create or replace function IsOfficeAllow(userID number, door_code varchar2)
  return number is
  Result  number := 0;
  A_count number := 0;
  UserIDx number;
begin
  if userID > 0 then
    begin
      select count(1)
        into A_count
        from rel_accounting_offices ao
        left join lib_offices o
          on o.id = ao.office
       where  
		ao.office > 0 
		and o.active = 1
	and ao.worker = userID;
    exception
      WHEN OTHERS THEN
        A_count := 0;
    end;
    if A_count = 0 then
      return 1;
    end if;
    begin
      select ao.worker
        into UserIDx
        from rel_accounting_offices ao
        left join lib_offices o
          on o.id = ao.office
        left join lib_doors d
          on d.office = o.id
       where d.code = door_code
		and o.active = 1
         and ao.worker = userID;
      if UserIDx > 0 then
        Result := 1;
      end if;
    exception
      WHEN OTHERS THEN
        Result := 0;
    end;
  end if;
  return(Result);
end IsOfficeAllow;