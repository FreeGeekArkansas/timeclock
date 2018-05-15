-- Deploy timeclock:states to pg
 BEGIN;

CREATE
    TABLE
        states(
            state_id serial PRIMARY KEY,
            name text
        );

\COPY states (name) FROM 'deploy/states.csv'(FORMAT 'csv', HEADER TRUE);
       
COMMIT;
