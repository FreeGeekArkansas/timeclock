-- Revert timeclock:countries from pg
 BEGIN;

DROP
    TABLE
        countries;

COMMIT;
