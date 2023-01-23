CREATE OR REPLACE VIEW HRS_WORKERS_ALL AS
SELECT rpo.ID,
       FIRSTNAME,
       LASTNAME,
       EMAIL,
       PERMIT_ID,
       USER_ROLE,
       hwd.ACTIVE,
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
       rpo.person          parent_id,
       rpo.ORG,
       HWd.IBAN,
       lu.LIB_TITLE        org_name
  FROM slf_persons hwd
  LEFT JOIN rel_person_org rpo
    ON HWD.ID = rpo.person
  LEFT JOIN LIB_UNITORGS lu
    ON lu.ID = rpo.ORG
 WHERE lu.active = 1