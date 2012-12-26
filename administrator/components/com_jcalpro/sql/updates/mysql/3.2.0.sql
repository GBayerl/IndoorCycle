#############################################
##          Fields Table - Assets          ##
#############################################;

ALTER TABLE #__jcalpro_fields ADD
	`asset_id` int(10) unsigned NOT NULL default '0'
	COMMENT 'FK to the #__assets table.'
	AFTER `id`
;
