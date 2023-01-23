CREATE OR REPLACE function GetLatenes(user_id number, Start_date date)
  return number is
  v_Result     number;
  v_end_date date;
BEGIN
  SELECT
  m.event_date
    INTO
  v_end_date
FROM
  (
  SELECT
    ee.event_date,
    ROW_NUMBER() OVER(PARTITION BY ee.staff_id ORDER BY ee.event_date ASC) rn
  FROM
    HRS_STAFF_EVENTS ee
  WHERE
    ee.staff_id = user_id
    and ee.event_date between Start_date and Start_date + 2
    AND ee.real_type_id = 3500) m
WHERE
  m.rn = 1;

SELECT
  sum(nvl(m.time_min, 0)) / 60
    INTO
  v_Result
FROM
  hrs_staff_events m
WHERE
  m.event_date >= Start_date
  AND m.event_date <= v_end_date
  AND m.c_resolution <> 1
  AND m.staff_id = user_id;
RETURN v_Result;
END GetLatenes;
