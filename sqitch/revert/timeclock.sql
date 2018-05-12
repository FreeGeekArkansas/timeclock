-- Revert volunteer:timeclock from pg

BEGIN;

DROP TABLE timeclock;
DROP TABLE purposes;

COMMIT;
