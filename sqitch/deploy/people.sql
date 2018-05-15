-- Deploy timeclock:people to pg
-- TODO
-- state_other, state foreignkey ref
-- emergency_relationship as foreignkey or enum
-- country as foreignkey

BEGIN;

CREATE
    TYPE person_types AS ENUM(
        'emergency contact',
        'customer',
        'guest',
        'volunteer',
        'employee'
    );

CREATE
    TYPE access_types AS ENUM(
        'normal',
        'verifier',
        'admin'
    );

 CREATE
    TABLE
        people(
            person_id serial PRIMARY KEY,
            first_name text NOT NULL,
            middle_name text,
            last_name text,
            address1 text,
            address2 text,
            city text,
            state_id int REFERENCES states,
            state_other text,
            zipcode text,
            country_id int REFERENCES countries,
            phone text,
            email text,
            dob DATE,
            guardian INT REFERENCES people(person_id),
            emergency_contact INT REFERENCES people,
            emergency_relationship text,
            TYPE person_types,
            access access_types,
            created_on timestamptz DEFAULT now(),
            created_by INT REFERENCES people(person_id),
            created_from inet,
            modified_on timestamptz DEFAULT now(),
            modified_by INT REFERENCES people(person_id),
            modified_from inet,
            verified_on timestamptz,
            verified_by INT REFERENCES people(person_id),
            verified_from inet
        );

-- password should be hashed and salted with SHA512
 -- token will be text to use instead of username/pin to login
 CREATE
    TABLE
        authentication(
            person_id INT REFERENCES people PRIMARY key,
            username text,
            pin text,
            password text,
            access_card text
        );



COMMIT;
