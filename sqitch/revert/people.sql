-- Revert volunteer:people from pg

BEGIN;

DROP TABLE authentication;
DROP TABLE people;
DROP TYPE person_types;
DROP TYPE access_types;

COMMIT;

