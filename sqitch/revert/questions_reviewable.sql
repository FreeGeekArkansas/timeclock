-- Revert timeclock:questions_reviewable from pg

BEGIN;

alter table questions drop reviewable_answer;

COMMIT;
