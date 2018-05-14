-- Deploy timeclock:countries to pg
 BEGIN;

CREATE
    TABLE
        countries(
            country_id serial PRIMARY KEY,
            name text
        );

\COPY countries (name) FROM 'deploy/countries.csv'(FORMAT 'csv',HEADER FALSE);


COMMIT;
