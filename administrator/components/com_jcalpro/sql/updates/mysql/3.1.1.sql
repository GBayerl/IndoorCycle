#############################################
##    Main Events Table - Language Code    ##
#############################################;

ALTER TABLE #__jcalpro_events ADD
	`language` char(7) NOT NULL default '*'
	COMMENT 'Event language code'
	AFTER `description`
;

#############################################
##    Main Events Table - Extra Indexes    ##
#############################################;

CREATE INDEX end_date
	ON #__jcalpro_events (end_date)
;

CREATE INDEX published
	ON #__jcalpro_events (published)
;

CREATE INDEX approved
	ON #__jcalpro_events (approved)
;

CREATE INDEX idx_language
	ON #__jcalpro_events (language)
;

CREATE INDEX idx_start_end_date
	ON #__jcalpro_events (start_date, end_date)
;

CREATE INDEX idx_published_approved
	ON #__jcalpro_events (published, approved)
;

CREATE INDEX idx_private_created_by
	ON #__jcalpro_events (private, created_by)
;
