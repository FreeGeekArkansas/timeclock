-- Deploy volunteer:answers to pg
 BEGIN;

CREATE
    TABLE
        answers(
            answer_id serial PRIMARY KEY,
	    question_id int references questions,
            text_answer text,
            bool_answer bool,
            answered_by int REFERENCES people(person_id),
            answered_on timestamptz

        );

COMMIT;
