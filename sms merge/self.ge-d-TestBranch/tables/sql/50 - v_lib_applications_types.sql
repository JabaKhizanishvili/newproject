CREATE OR REPLACE  VIEW V_LIB_APPLICATIONS_TYPES  as
  select t.id, t.type, t.lib_title, t.lib_desc, t.active
  from LIB_APPLICATIONS_TYPES t
union all
select tt.id, tt.id, tt.lib_title, tt.lib_desc, tt.active
  from LIB_LIMIT_APP_TYPES tt