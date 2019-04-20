-- Deploy timeclock:questions_legal to pg

BEGIN;

  INSERT
    INTO
        questions(
            question,
            optional,
            answer_type,
            created_on,
	    reviewable_answer
        )
    VALUES(
        'I have read, I understand, and I agree to comply with the Free Geek Arkansas Policies & Procedures. I certify that the answers I have given in this application are true and complete to the best of my knowledge. I accept membership in the organization and offer to volunteer my services to Free Geek Arkansas with the understanding that there is no monetary compensation for those services. I shall perform the work at my won risk. Free Geek Arkansas shall not be responsible for any loss to my property for any reason, including any alleged negligence of Free Geek Arkansas. Livewise, Free Geek Arkansas shall not be liable to me or any of my employees or agents for any damages or personal injuries for any reason, including any alleged negligence of Free Geek Arkansas. I shall indemnify and hold harmless Free Geek Arkansas from any claim, demand, loss, liabiity, damage, or expense arising in any way from my work.',
        FALSE,
        'boolean',
        now(),
	FALSE
    );

COMMIT;
