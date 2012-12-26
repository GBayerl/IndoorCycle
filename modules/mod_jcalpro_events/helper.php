<?php
/**
 * @version		$Id: helper.php 828 2012-10-24 20:36:01Z jeffchannell $
 * @package		JCalPro
 * @subpackage	mod_jcalpro_events

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

jimport('joomla.application.component.model');

JLoader::register('JCalPro', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/jcalpro.php');
JLoader::register('JCalProBaseModel', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/models/basemodel.php');
JCalProBaseModel::addIncludePath(JPATH_SITE.'/components/com_jcalpro/models', 'JCalProModel');

abstract class modJCalProEventsHelper
{
	const RANGE_PAST_EVENTS     = 1;
	const RANGE_UPCOMING_EVENTS = 2;
	const RANGE_THIS_WEEK       = 3;
	const RANGE_LAST_WEEK       = 4;
	const RANGE_NEXT_WEEK       = 5;
	const RANGE_THIS_MONTH      = 6;
	const RANGE_LAST_MONTH      = 7;
	const RANGE_NEXT_MONTH      = 8;
	const RANGE_TODAY           = 9;
	const RANGE_TOMORROW        = 10;
	const RANGE_YESTERDAY       = 11;
	
	public static function getList(&$params) {
		
		$profiler = JProfiler::getInstance('Application');
		$profiler->mark('onJCalProCalendarModuleGetListStart');
		
		// Get the dbo
		$db = JFactory::getDbo();

		// Get an instance of the events model
		$model = JCalProBaseModel::getInstance('Events', 'JCalProModel', array('ignore_request' => true));
		
		$range = (int) $params->get('filter_date_range', modJCalProEventsHelper::RANGE_UPCOMING_EVENTS);
		
		// set the state based on the module params
		$model->setState('filter.category', $params->get('filter_category', array()));
		$model->setState('list.limit', $params->get('list_limit', 5));
		
		switch ($range) {
			// events from the past should be ordered in reverse
			case modJCalProEventsHelper::RANGE_PAST_EVENTS:
			case modJCalProEventsHelper::RANGE_LAST_WEEK:
			case modJCalProEventsHelper::RANGE_LAST_MONTH:
			case modJCalProEventsHelper::RANGE_YESTERDAY:
				$model->setState('list.ordering', 'Event.start_date');
				$model->setState('list.direction', 'DESC');
				// NOTE: no break here!!!
			default:
				$model->setState('filter.date_range', $range);
		}
				
		// handle filters
		$filters = $model->getCategoryFilters();
		$invert  = $model->getCategoryFiltersInvert();
		$model->setCategoryFilters($params->get('filter_category', array()));
		$model->setCategoryFiltersInvert($params->get('filter_category_invert', false));
		
		// get the events from the model
		$items = $model->getItems();
		
		// we're going to alter the items based on these params
		$display_date = (int) $params->get('display_date', 1);
		$date_format  = $params->get('date_format', '');
		
		// loop items and prepare them for the module
		if (!empty($items)) foreach ($items as &$item) {
			// set initial values
			$item->mod_events_date = '';
			// set the display date
			if ($display_date) {
				$item->mod_events_date = $item->user_minidisplay;
				// check if we have a format & attempt to translate
				if (!empty($date_format)) {
					try {
						$mod_events_date = $item->user_datetime->format($date_format);
					}
					catch (Exception $e) {
						// cannot set :(
						$mod_events_date = false;
					}
					if ($mod_events_date) $item->mod_events_date = $mod_events_date;
				}
			}
		}
		
		// reset state
		$model->setCategoryFilters($filters);
		$model->setCategoryFiltersInvert($invert);
		
		$profiler->mark('onJCalProCalendarModuleGetListEnd');

		return $items;
	}
}