-- Revert volunteer:answers from pg

BEGIN;

DROP TABLE answers;

COMMIT;
