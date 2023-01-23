CREATE OR REPLACE VIEW HRS_WORKERS_SCH AS
  SELECT sw.ID,
       FIRSTNAME,
       LASTNAME,
       EMAIL,
       PERMIT_ID,
       USER_ROLE,
       CATEGORY_ID,
       sw.ACTIVE,
       LDAP_USERNAME,
       MOBILE_PHONE_NUMBER,
       PHOTO,
       CHANGE_DATE,
       SMS_REMINDER,
       U_PASSWORD,
       LIVELIST,
       PRIVATE_NUMBER,
       GENDER,
       NATIONALITY,
       hwd.country_code,
       BIRTHDATE,
       TIMECONTROL,
       COUNTING_TYPE,
       sw.person           parent_id,
       sw.orgpid,
       sw.ORG,
       TABLENUM,
       p.lib_title         POSITION,
       sw.SALARY,
       ss.ORG_PLACE,
       CHIEFS,
       GRAPHTYPE,
       CALCULUS_REGIME,
       CONTRACTS_DATE,
       CONTRACT_END_DATE,
       HWD.IBAN,
       sw.staff_schedule,
       lu.LIB_TITLE        org_name,
       u.LIB_TITLE         org_place_name,
       sw.client_id,
       sw.active enable,
       hwd.father_name,
       sw.p_code
  FROM slf_worker sw
 inner join rel_person_org rpo
    on rpo.id = sw.orgpid
  left join lib_staff_schedules ss
    on ss.id = sw.staff_schedule
 inner JOIN slf_persons hwd
    ON HWD.ID = rpo.person
  LEFT JOIN LIB_UNITORGS lu
    ON lu.ID = sw.ORG
  LEFT JOIN LIB_UNITS u
    ON u.ID = ss.org_place
  left join lib_staff_schedules ss
    on ss.id = sw.staff_schedule
  left join lib_positions p
    on p.id = ss.position