-- Revert timeclock:states from pg
 BEGIN;

DROP
    TABLE
        states;

COMMIT;
