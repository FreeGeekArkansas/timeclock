-- Revert volunteer:questions from pg

BEGIN;

DROP TABLE questions;
DROP TYPE answer_types;

COMMIT;
