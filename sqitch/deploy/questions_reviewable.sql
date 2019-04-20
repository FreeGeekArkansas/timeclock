-- Deploy timeclock:questions_reviewable to pg

BEGIN;

alter table questions add reviewable_answer boolean;

UPDATE questions SET reviewable_answer = true where question = 'Do you have any work related restrictions you''d like us to know about?';
UPDATE questions SET reviewable_answer = false where question = 'Are you legally allowed to be around children?';
UPDATE questions SET reviewable_answer = true where question = 'Do you have any restraining orders against you?';

COMMIT;
