#############################################
##            Main Events Table            ##
#############################################;

ALTER TABLE #__jcalpro_events ADD
	`location` int(11) NOT NULL default '0'
	COMMENT 'Primary key of location'
	AFTER `common_event_id`
;

#############################################
##             Locations Table             ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jcalpro_locations` (

	`id` int(11)	NOT NULL auto_increment
	COMMENT 'Primary Key',
	 
	`title` varchar(255) NOT NULL default ''
	COMMENT 'Location Title',
	 
	`alias` varchar(255) NOT NULL default ''
	COMMENT 'Location Alias (for URLs)',
	 
	`address` varchar(255) NOT NULL default ''
	COMMENT 'Location Address',
	 
	`city` varchar(100) NOT NULL default ''
	COMMENT 'Location City',
	 
	`latitude` float(16,12) NOT NULL default 0.0
	COMMENT 'Location Latitude',
	 
	`longitude` float(16,12) NOT NULL default 0.0
	COMMENT 'Location Longitude',
	 
	`latlng` Point NOT NULL
	COMMENT 'Location point',
	
	`created` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when Location was created',	
	 
	`created_by` int(11) NOT NULL default '0'
	COMMENT 'User id of Location creator',
	
	`modified` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'when Location was last modified',	
	 
	`modified_by` int(11) NOT NULL default '0'
	COMMENT 'User id of last modifier',
	 
	`published` tinyint(1) default '0'
	COMMENT 'Publication status',
	
	`checked_out` int(11) unsigned NOT NULL default '0'
	COMMENT 'Locking column to prevent simultaneous updates',
	
	`checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00'
	COMMENT 'Date and Time Location was checked out',
	 
	PRIMARY KEY (id)
) ENGINE=MyISAM;