declare
  l_is_matching_row number := 0;
begin
  begin
    select count(*)
      into l_is_matching_row
      from lib_languages
     where lib_code = 'ka';
    if (l_is_matching_row = 0) then
      insert into lib_languages
        (id,
         lib_code,
         lib_title,
         active,
         change_date,
         change_user,
         lib_desc,
         def_lang)
      values
        (sqs_lib_languages.nextval,
         'ka',
         'Georgian',
         '1',
         sysdate,
         -1,
         null,
         1);
      commit;
    end if;
  exception
    when DUP_VAL_ON_INDEX then
      ROLLBACK;
  end;
  begin
    select count(*)
      into l_is_matching_row
      from lib_languages
     where lib_code = 'en';
    if (l_is_matching_row = 0) then
      insert into lib_languages
        (id,
         lib_code,
         lib_title,
         active,
         change_date,
         change_user,
         lib_desc,
         def_lang)
      values
        (sqs_lib_languages.nextval,
         'en',
         'English',
         '0',
         sysdate,
         -1,
         null,
         0);
      commit;
    end if;
  exception
    when DUP_VAL_ON_INDEX then
      ROLLBACK;
  end;
end;
