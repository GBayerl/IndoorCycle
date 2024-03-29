<?php
/**
 * @version		$Id: location.php 773 2012-04-18 00:44:30Z jeffchannell $
 * @package		JCalPro
 * @subpackage	com_jcalpro

**********************************************
JCal Pro
Copyright (c) 2006-2012 Anything-Digital.com
**********************************************
JCalPro is a native Joomla! calendar component for Joomla!

JCal Pro was once a fork of the existing Extcalendar component for Joomla!
(com_extcal_0_9_2_RC4.zip from mamboguru.com).
Extcal (http://sourceforge.net/projects/extcal) was renamed
and adapted to become a Mambo/Joomla! component by
Matthew Friedman, and further modified by David McKinnis
(mamboguru.com) to repair some security holes.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This header must not be removed. Additional contributions/changes
may be added to this header as long as no information is deleted.
**********************************************
Get the latest version of JCal Pro at:
http://anything-digital.com/
**********************************************

 */

defined('JPATH_PLATFORM') or die;

JLoader::register('JCalPro', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/jcalpro.php');
JLoader::register('JCalProAdminModel', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/models/basemodeladmin.php');

/**
 * This models supports retrieving a location.
 *
 * @package		JCalPro
 * @subpackage	com_jcalpro
 */
class JCalProModelLocation extends JCalProAdminModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jcalpro.location';
	
	public function getItem($id = null) {
		// get our item
		$item = parent::getItem($id);
		// get the events model & load the recent & upcoming events for this location
		if ($item) {
			// get our events model
			$baseModel  = JCalPro::getModelInstance('Events', 'JCalProModel');
			$layout     = $baseModel->getState('filter.layout');
			$location   = $baseModel->getState('filter.location');
			$date_range = $baseModel->getState('filter.date_range');
			// both our queries here will use this location
			$baseModel->setState('filter.layout', 'location');
			$baseModel->setState('filter.location', $item->id);
			// past events
			$baseModel->setState('filter.date_range', 1);
			$item->past_events = $baseModel->getItems();
			// upcoming events
			$baseModel->setState('filter.date_range', 2);
			$item->upcoming_events = $baseModel->getItems();
			// reset the state
			$baseModel->setState('filter.layout', $layout);
			$baseModel->setState('filter.location', $location);
			$baseModel->setState('filter.date_range', $date_range);
		}
		// remember to send our item along!
		return $item;
	}
}
