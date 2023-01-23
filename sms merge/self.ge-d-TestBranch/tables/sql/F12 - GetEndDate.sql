CREATE OR REPLACE function GetEndDate(user_id number, Start_date date)
  return date is
  Result date;
BEGIN
	SELECT
	m.event_date
    INTO Result
	FROM
		(
		SELECT
			ee.event_date,
			ROW_NUMBER() OVER(PARTITION BY ee.staff_id ORDER BY ee.event_date ASC) rn
		FROM
			HRS_STAFF_EVENTS ee
		WHERE
			ee.staff_id = user_id
			AND ee.event_date BETWEEN Start_date AND Start_date + 2
			AND ee.real_type_id = 3500) m
	WHERE
		m.rn = 1;

	RETURN Result;
END GetEndDate;
