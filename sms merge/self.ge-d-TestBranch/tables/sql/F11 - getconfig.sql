create or replace function getconfig(p_key varchar2) return varchar2 is
  result varchar2(4000);
begin
  begin
  select t.value into result from system_config t where t.key = p_key;
  return(result);
  exception when others then
      return '0';
  end;
end getconfig;
