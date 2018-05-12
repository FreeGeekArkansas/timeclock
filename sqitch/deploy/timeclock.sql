-- Deploy volunteer:timeclock to pg
 BEGIN;

CREATE
    TABLE
        purposes(
            purpose_id serial PRIMARY KEY,
            purpose text,
            type_required person_types DEFAULT 'guest'
        );

CREATE
    TABLE
        timeclock(
            timeclock_id serial PRIMARY KEY,
            clock_in timestamptz NOT NULL,
            clock_out timestamptz,
            purpose_id INT REFERENCES purposes NOT NULL,
            other_purpose text,
            person_id INT REFERENCES people NOT NULL,
            modified_on timestamptz,
            modified_by INT REFERENCES people
        );

INSERT
    INTO
        purposes(purpose)
    VALUES('Internet Cafe');

INSERT
    INTO
        purposes(purpose)
    VALUES('volunteered');

INSERT
    INTO
        purposes(
            purpose,
            type_required
        )
    VALUES(
        'worked for pay',
        'employee'
    );

INSERT
    INTO
        purposes(purpose)
    VALUES('worked on own project');

INSERT
    INTO
        purposes(purpose)
    VALUES('taught a class');

INSERT
    INTO
        purposes(purpose)
    VALUES('took a class');

INSERT
    INTO
        purposes(purpose)
    VALUES('other');

COMMIT;
