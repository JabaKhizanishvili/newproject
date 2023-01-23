declare
  l_is_matching_row number := 0;
begin

  begin
    select count(*)
      into l_is_matching_row
      from lib_menus
     where lib_option = 'languages';
    if (l_is_matching_row = 0) then
      insert into lib_menus
        (id,
         lib_level,
         lib_show,
         lib_title,
         lib_parent,
         lib_option,
         active,
         ordering,
         lib_desc,
         client_id,
         icon)
      values
        (library.nextval,
         1,
         1,
         'სისტემის ენები',
         (select m.lib_parent
            from lib_menus m
           where m.lib_option = 'configs'),
         'languages',
         1,
         750,
         '',
         0,
         null);
      commit;
    end if;
  exception
    when DUP_VAL_ON_INDEX then
      ROLLBACK;
  end;
--   begin
--     select count(*)
--       into l_is_matching_row
--       from lib_menus
--      where lib_option = 'language';
--     if (l_is_matching_row = 0) then
--       insert into lib_menus
--         (id,
--          lib_level,
--          lib_show,
--          lib_title,
--          lib_parent,
--          lib_option,
--          active,
--          ordering,
--          lib_desc,
--          client_id,
--          icon)
--       values
--         (library.nextval,
--          2,
--          0,
--          'სისტემის ენები',
--          (select m.id from lib_menus m where m.lib_option = 'languages'),
--          'language',
--          1,
--          750,
--          '',
--          0,
--          null);
--       commit;
--     end if;
--   exception
--     when DUP_VAL_ON_INDEX then
--       ROLLBACK;
--   end;
end;