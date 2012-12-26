CREATE TABLE IF NOT EXISTS `#__joompro_subscriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Automatic incrementing key field',
  `catid` int(11) NOT NULL DEFAULT '0' COMMENT 'Foreign key to #__categories table',
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT 'Title of Subscription',
  `alias` varchar(255) NOT NULL DEFAULT '' COMMENT 'Alias value, used for SEF URLs',
  `description` text NOT NULL COMMENT 'Description (will be edited using editor)',
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Foreign key to #__groups table',
  `duration` int(11) NOT NULL DEFAULT '0' COMMENT 'Number for days that subscription lasts',
  `published` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Published state (1=published, 0=unpublished, -2=trashed)',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(11) NOT NULL DEFAULT '1' COMMENT 'Used to control access to subscriptions',
  `params` text NOT NULL COMMENT 'For possible future use to add item-level parameters (JSON string format)',
  `language` char(7) NOT NULL DEFAULT '' COMMENT 'For possible future use to add language switching',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign key to #__users table for user who created this item',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign key to #__users table for user who modified this item',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date to start publishing this item',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date to stop publishing this item',
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_published` (`published`),
  KEY `idx_catid` (`catid`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_language` (`language`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__joompro_sub_mapping` (
  `subscription_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #_joompro_subscriptions.id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__users.id',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`subscription_id`, `user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;