-- Deploy volunteer:questions to pg
 BEGIN;

CREATE
    TYPE answer_types AS ENUM(
        'text',
        'boolean'
    );

CREATE
    TABLE
        questions(
            question_id serial PRIMARY KEY,
            question text,
            optional bool,
            answer_type answer_types,
            created_on timestamptz,
            created_by INT REFERENCES people(person_id),
            modified_on timestamptz,
            modified_by INT REFERENCES people(person_id)
        );

COMMIT;
