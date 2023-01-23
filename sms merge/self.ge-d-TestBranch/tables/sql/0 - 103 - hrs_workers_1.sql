CREATE OR REPLACE VIEW hrs_workers AS
SELECT rpo.ID,
       FIRSTNAME,
       LASTNAME,
       EMAIL,
       PERMIT_ID,
       USER_ROLE,
       rpo.ACTIVE,
       LDAP_USERNAME,
       MOBILE_PHONE_NUMBER,
       PHOTO,
       SMS_REMINDER,
       U_PASSWORD,
       LIVELIST,
       PRIVATE_NUMBER,
       GENDER,
       NATIONALITY,
       BIRTHDATE,
       TIMECONTROL,
       rpo.person parent_id,
       rpo.ORG,
       HWd.IBAN,
       lu.LIB_TITLE org_name,
	  hwd.father_name
  FROM slf_persons hwd
  LEFT JOIN rel_person_org rpo
    ON HWD.ID = rpo.person
  LEFT JOIN LIB_UNITORGS lu
    ON lu.ID = rpo.ORG
--left join slf_worker w on w.orgpid = rpo.id
 WHERE hwd.ACTIVE = 1
   and rpo.id in (select w.orgpid from slf_worker w where w.active = 1)
   and lu.active = 1
