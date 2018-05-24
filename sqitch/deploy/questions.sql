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
INSERT
    INTO
        questions(
            question,
            optional,
            answer_type,
            created_on
        )
    VALUES(
        'Do you have any work related restrictions you''d like us to know about?',
        TRUE,
        'text',
        now()- '1 week'::INTERVAL
    );

INSERT
    INTO
        questions(
            question,
            optional,
            answer_type,
            created_on
        )
    VALUES(
        'Are you legally allowed to be around children?',
        FALSE,
        'boolean',
        now()- '1 week'::INTERVAL
    );

INSERT
    INTO
        questions(
            question,
            optional,
            answer_type,
            created_on
        )
    VALUES(
        'Do you have any restraining orders against you?',
        FALSE,
        'boolean',
        now()- '1 week'::INTERVAL
    );
   
   
   INSERT
    INTO
        questions(
            question,
            optional,
            answer_type,
            created_on
        )
    VALUES(
        'Are you here to complete community service hours?',
        TRUE,
        'boolean',
        now()- '1 week'::INTERVAL
    );

   INSERT
    INTO
        questions(
            question,
            optional,
            answer_type,
            created_on
        )
    VALUES(
        'What skills, training or knowledge (computer related or not) would you be interested in sharing with Free Geek Arkansas?',
        TRUE,
        'text',
        now()- '1 week'::INTERVAL
    );

   COMMIT;
