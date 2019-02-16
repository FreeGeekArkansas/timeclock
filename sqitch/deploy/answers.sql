-- Deploy volunteer:answers to pg
 BEGIN;

CREATE
    TABLE
        answers(
            answer_id serial PRIMARY KEY,
            question_id INT REFERENCES questions,
            text_answer text,
            bool_answer bool,
            answered_by INT REFERENCES people(person_id),
            answered_on timestamptz,
            UNIQUE (question_id,answered_by)
        );

COMMIT;
