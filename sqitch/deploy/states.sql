-- Deploy timeclock:states to pg
 BEGIN;

CREATE
    TABLE
        states(
            state_id serial PRIMARY KEY,
            name text
        );

COMMIT;
