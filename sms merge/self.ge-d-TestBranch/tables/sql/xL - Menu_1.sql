declare
  l_is_matching_row number := 0;
begin
  begin
    select count(*)
      into l_is_matching_row
      from lib_menus
     where lib_option = 'change_password';
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
         'პაროლის შეცვლა',
         (select m.lib_parent
            from lib_menus m
           where m.lib_option = 'profileedit'),
         'change_password',
         1,
         900,
         '',
         0,
         null);

	insert into REL_ROLES_MENUS
	SELECT
		lr.ID , (SELECT lm.id from LIB_MENUS lm WHERE lm.LIB_OPTION = 'change_password') menu_id, '[]' p, 0 cl
	FROM
		LIB_ROLES lr
	WHERE
		lr.ACTIVE = 1;
      commit;
	UPDATE 
		LIB_MENUS lm set lm.LIB_TITLE  = trim(REPLACE(REPLACE(lm.LIB_TITLE, 'პაროლის ცვლილება', ''), '/', ''))
	WHERE
		lm.LIB_OPTION = 'profileedit'
		AND lm.LIB_TITLE LIKE '%პაროლის ცვლილება%';
    end if;
  exception
    when DUP_VAL_ON_INDEX then
      ROLLBACK;
  end;
end;