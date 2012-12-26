<?php
/**
 * @version		$Id: install.php 800 2012-09-06 02:33:01Z jeffchannell $
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

JLoader::register('JCalProBaseModel', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/models/basemodel.php');

JLoader::register('JCalPro', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/jcalpro.php');
JLoader::register('JCalProHelperDate', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/date.php');


// ensure our language files are properly loaded
JCalPro::language('com_jcalpro', JPATH_ADMINISTRATOR);
JCalPro::language('com_jcalpro.event', JPATH_ADMINISTRATOR);

/**
 * This model is for the install options
 *
 * @package		JCalPro
 * @subpackage	com_jcalpro
 */
class JCalProModelInstall extends JCalProBaseModel
{
	public function getData($layout) {
		switch ($layout) {
			case 'sample' : return $this->_installSampleData();
			case 'migrate': return $this->_installMigrationData();
			default       :
				$this->setError(JText::_('COM_JCALPRO_INSTALLER_LAYOUT_NOT_FOUND'));
				return false;
		}
	}
	
	public function checkCategories() {
		static $hasCategories;
		if (is_null($hasCategories)) {
			// our database object
			$db = JFactory::getDbo();
			// check if there are any categories - if not, offer to install sample data
			$db->setQuery((string) $db->getQuery(true)
				->select('COUNT(id) AS c')
				->from('#__categories')
				->where('extension = "com_jcalpro"')
			);
			try {
				$hasCategories = (bool) $db->loadResult();
			}
			catch (Exception $e) {
				$hasCategories = false;
			}
		}
		return $hasCategories;
	}
	
	public function checkMigration() {
		static $canMigrate;
		if (is_null($canMigrate)) {
			$canMigrate = !$this->checkCategories();
			if (!$canMigrate) {
				// our database object
				$db = JFactory::getDbo();
				// check for old data from JCal Pro 2
				foreach (array('calendars', 'categories', 'events', 'config') as $table) {
					$db->setQuery('SHOW TABLES LIKE "%_jcalpro2_' . $table . '"');
					try {
						$tableCanMigrate = $db->loadResult();
					}
					catch (Exception $e) {
						$tableCanMigrate = false;
					}
					$canMigrate = $canMigrate && (bool) $tableCanMigrate;
				}
			}
		}
		return $canMigrate;
	}
	
	private function _installMigrationData() {
		if (!$this->checkMigration()) {
			$this->setError(JText::_('COM_JCALPRO_MIGRATION_NO_DATA'));
			return false;
		}
		
		// some stuff we need
		jimport('jcaldate.date');
		jimport('jcaldate.timezone');
		$db = JFactory::getDbo();
		
		// we need to start with calendars, then categories, then events
		foreach (array('calendars', 'categories', 'events', 'config') as $table) {
			$db->setQuery($db->getQuery(true)
				->select('*')
				->from('#__jcalpro2_' . $table)
			);
			try {
				$$table = $db->loadObjectList();
			}
			catch (Exception $e) {
				$$table = false;
			}
			if (empty($$table)) {
				$this->setError(JText::_('COM_JCALPRO_MIGRATION_NO_' . strtoupper($table)));
				return false;
			}
		}
		
		// create our custom form & our fields to mimic v2 contact info
		// need "email", "url", and "contact" fields
		$fields = array(
			array(
				'name'          => 'email'
			,	'title'         => JText::_('COM_JCALPRO_MIGRATION_FIELD_EMAIL_TITLE')
			,	'type'          => 'email'
			,	'description'   => JText::_('COM_JCALPRO_MIGRATION_FIELD_EMAIL_DESCRIPTION')
			,	'published'     => 1
			,	'formtype'      => 0
			,	'event_display' => 1
			,	'params'        => '{"classname":"inputbox"}'
			)
		,	array(
				'name'          => 'url'
			,	'title'         => JText::_('COM_JCALPRO_MIGRATION_FIELD_URL_TITLE')
			,	'type'          => 'url'
			,	'description'   => JText::_('COM_JCALPRO_MIGRATION_FIELD_URL_DESCRIPTION')
			,	'published'     => 1
			,	'formtype'      => 0
			,	'event_display' => 1
			,	'params'        => '{"classname":"inputbox"}'
			)
		,	array(
				'name'          => 'contact'
			,	'title'         => JText::_('COM_JCALPRO_MIGRATION_FIELD_CONTACT_TITLE')
			,	'type'          => 'textarea'
			,	'description'   => JText::_('COM_JCALPRO_MIGRATION_FIELD_CONTACT_DESCRIPTION')
			,	'published'     => 1
			,	'formtype'      => 0
			,	'event_display' => 1
			,	'params'        => '{"classname":"inputbox","attrs":{"cols":"50","rows":"5"}}'
			)
		);
		$fieldids = array();
		foreach ($fields as $field) {
			$table = JTable::getInstance('Field', 'JCalProTable');
			if (!$table->bind($field)) {
				$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_BIND_ERROR', JText::_('COM_JCALPRO_FIELD'), $field['title'], $table->getError()));
				return false;
			}
			if (!$table->check()) {
				$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_CHECK_ERROR', JText::_('COM_JCALPRO_FIELD'), $field['title'], $table->getError()));
				return false;
			}
			if (!$table->store()) {
				$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_STORE_ERROR', JText::_('COM_JCALPRO_FIELD'), $field['title'], $table->getError()));
				return false;
			}
			$fieldids[] = $table->id;
		}
		// now create the form
		$form = array(
			'title'      => JText::_('COM_JCALPRO_MIGRATION_FORM_EVENT_TITLE')
		,	'type'       => 0
		,	'published'  => 1
		,	'default'    => 1
		, 'formfields' => implode('|', $fieldids)
		);
		$formid = 0;
		$table = JTable::getInstance('Form', 'JCalProTable');
		if (!$table->bind($form)) {
			$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_BIND_ERROR', JText::_('COM_JCALPRO_FORM'), $form['title'], $table->getError()));
			return false;
		}
		if (!$table->check()) {
			$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_CHECK_ERROR', JText::_('COM_JCALPRO_FORM'), $form['title'], $table->getError()));
			return false;
		}
		if (!$table->store()) {
			$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_STORE_ERROR', JText::_('COM_JCALPRO_FORM'), $form['title'], $table->getError()));
			return false;
		}
		$formid = $table->id;
		
		// get our timezone
		$timezone = JCalTimeZone::joomla();
		$tz = false;
		foreach ($config as $cfg) {
			if ('site_timezone' == $cfg->name) {
				try {
					$tz = new JCalTimeZone($cfg->value);
				}
				catch (Exception $e) {
					// do nothing
				}
				break;
			}
		}
		if ($tz) $timezone = $tz;
		// get our base rules
		$rules = $this->_getRules();
		// v2 calendar > v3 category xref
		$calXref = array();
		// the first thing we need to do is create root categories for our calendars
		foreach ($calendars as $calendar) {
			// create our data for binding
			$data = array(
				'title'       => $calendar->cal_name
			,	'description' => $calendar->description
			,	'extension'   => 'com_jcalpro'
			,	'parent_id'   => 1
			,	'published'   => $calendar->published
			,	'access'      => 1
			,	'language'    => '*'
			,	'rules'       => $rules
			,	'params' => array(
					'jcalpro_color' => 'FFFFFF'
				,	'jcalpro_eventform' => $formid
				)
			);
			$table = JTable::getInstance('Category');
			if (!$table->bind($data)) {
				$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_BIND_ERROR', JText::_('COM_JCALPRO_CATEGORY'), $data['title'], $table->getError()));
				return false;
			}
			if (!$table->check()) {
				$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_CHECK_ERROR', JText::_('COM_JCALPRO_CATEGORY'), $data['title'], $table->getError()));
				return false;
			}
			if (!$table->store()) {
				$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_STORE_ERROR', JText::_('COM_JCALPRO_CATEGORY'), $data['title'], $table->getError()));
				return false;
			}
			$table->moveByReference(0, 'last-child', $table->id);
			$calXref['cal' . $calendar->cal_id] = $table->id;
		}
		// in order to recreate the old heirarchy, we need to loop events
		// in v2, calendars and categories were independent of one another
		// however, in v3 we use the core categories only
		// anyways... loop the events here, create the categories if they haven't already been,
		// and go ahead and import the event too
		$catXref = array();
		foreach ($events as $event) {
			// TODO: fix detached events!!!
			if ($event->rec_id) continue;
			// ensure this event's calendar was imported
			if (!array_key_exists('cal' . $event->cal_id, $calXref)) continue;
			// create our category key
			$key = 'cat' . $event->cal_id . '_' . $event->cat;
			// if we haven't created this category yet, we need to do so
			if (!array_key_exists($key, $catXref)) {
				// loop the categories & find the one that matches, and when we do create a new category
				foreach ($categories as $category) {
					// go to the next category if this isn't ours
					if ($category->cat_id != $event->cat) continue;
					// create our data for binding
					$data = array(
						'title'       => $category->cat_name
					,	'description' => $category->description
					,	'extension'   => 'com_jcalpro'
					,	'parent_id'   => $calXref['cal' . $event->cal_id]
					,	'published'   => $category->published
					,	'access'      => 1
					,	'language'    => '*'
					,	'rules'       => $rules
					,	'params' => array(
							'jcalpro_color' => trim($category->color, '#')
						)
					);
					// now save the category
					$table = JTable::getInstance('Category');
					if (!$table->bind($data)) {
						$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_BIND_ERROR', JText::_('COM_JCALPRO_CATEGORY'), $data['title'], $table->getError()));
						return false;
					}
					if (!$table->check()) {
						$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_CHECK_ERROR', JText::_('COM_JCALPRO_CATEGORY'), $data['title'], $table->getError()));
						return false;
					}
					if (!$table->store()) {
						$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_STORE_ERROR', JText::_('COM_JCALPRO_CATEGORY'), $data['title'], $table->getError()));
						return false;
					}
					$table->moveByReference($calXref['cal' . $event->cal_id], 'last-child', $table->id);
					$catXref[$key] = $table->id;
					// all done
					break;
				}
			}
			list($recur_end_until, $tmp) = explode(' ', $event->recur_until);
			// once again, ensure we have a category before continuing
			if (!array_key_exists($key, $catXref)) continue;
			// create the base DateTime object for this event
			$date = JCalDate::createFromMySQLFormat($event->start_date, JCalTimeZone::utc())->toTimezone($timezone);
			// before creating the event data, we have to calculate the end time back out
			// "all day" and "no end" are easy - they will have dates set as constants
			$duration_type = 0;
			$end_days      = 0;
			$end_hours     = 0;
			$end_minutes   = 0;
			switch ($event->end_date) {
				case JCalPro::JCL_ALL_DAY_EVENT_END_DATE:
				case JCalPro::JCL_ALL_DAY_EVENT_END_DATE_LEGACY:
				case JCalPro::JCL_ALL_DAY_EVENT_END_DATE_LEGACY_2:
					$duration_type = JCalPro::JCL_EVENT_DURATION_ALL;
					break;
				case JCalPro::JCL_EVENT_NO_END_DATE:
					$duration_type = JCalPro::JCL_EVENT_DURATION_NONE;
					break;
				default:
					$duration_type = JCalPro::JCL_EVENT_DURATION_DATE;
					// this is nasty - we have to determine the values to use here
					// we COULD use DateTime::diff if we're on PHP 5.3, or if our internal date handler
					// had a complete PHP 5.2 backwards-compatible implementation
					// oh well - do it the hard way :'(
					$end = JCalDate::createFromMySQLFormat($event->end_date, JCalTimeZone::utc())->toTimezone($timezone);
					// days first - just check if the month & day are the same
					while (!($date->day() == $end->day() && $date->month() == $end->month() && $date->year() == $end->year())) {
						$end->subDay();
						$end_days++;
					}
					// handle hours
					while ($date->hour() != $end->hour()) {
						$end->subHour();
						$end_hours++;
					}
					// handle minutes
					while ($date->minute() != $end->minute()) {
						$end->subMin();
						$end_minutes++;
					}
					break;
			}
			// build a "params" array for things we ditched (email, etc)
			$contactinfo = array(
				'contact' => $event->contact
			,	'url'     => $event->url
			,	'email'   => $event->email
			);
			// now create the event data
			$data = array(
				'title'                    => $event->title
			,	'description'              => $event->description
			,	'common_event_id'          => $event->common_event_id
			,	'rec_id'                   => 0
			,	'detached_from_rec'        => 0
			,	'day'                      => $date->day()
			,	'month'                    => $date->month()
			,	'year'                     => $date->year()
			,	'hour'                     => $date->hour()
			,	'minute'                   => $date->minute()
			,	'timezone'                 => $date->timezone()
			,	'recur_type'               => $event->rec_type_select
			,	'recur_end_type'           => $event->recur_end_type
			,	'recur_end_count'          => $event->recur_count
			,	'recur_end_until'          => $recur_end_until
			,	'approved'                 => $event->approved
			,	'private'                  => $event->private
			,	'published'                => $event->published
			,	'rec_daily_period'         => $event->rec_daily_period
			,	'rec_weekly_period'        => $event->rec_weekly_period
			,	'rec_weekly_on_monday'     => $event->rec_weekly_on_monday
			,	'rec_weekly_on_tuesday'    => $event->rec_weekly_on_tuesday
			,	'rec_weekly_on_wednesday'  => $event->rec_weekly_on_wednesday
			,	'rec_weekly_on_thursday'   => $event->rec_weekly_on_thursday
			,	'rec_weekly_on_friday'     => $event->rec_weekly_on_friday
			,	'rec_weekly_on_saturday'   => $event->rec_weekly_on_saturday
			,	'rec_weekly_on_sunday'     => $event->rec_weekly_on_sunday
			,	'rec_monthly_period'       => $event->rec_monthly_period
			,	'rec_monthly_type'         => $event->rec_monthly_type
			,	'rec_monthly_day_number'   => $event->rec_monthly_day_number
			,	'rec_monthly_day_list'     => $event->rec_monthly_day_list
			,	'rec_monthly_day_order'    => $event->rec_monthly_day_order
			,	'rec_monthly_day_type'     => $event->rec_monthly_day_type
			,	'rec_yearly_period'        => $event->rec_yearly_period
			,	'rec_yearly_type'          => $event->rec_yearly_type
			,	'rec_yearly_on_month'      => $event->rec_yearly_on_month
			,	'rec_yearly_on_month_list' => $event->rec_yearly_on_month_list
			,	'rec_yearly_day_number'    => $event->rec_yearly_day_number
			,	'rec_yearly_day_order'     => $event->rec_yearly_day_order
			,	'rec_yearly_day_type'      => $event->rec_yearly_day_type
			,	'duration_type'            => $duration_type
			,	'end_days'                 => $end_days
			,	'end_hours'                => $end_hours
			,	'end_minutes'              => $end_minutes
			,	'params'                   => $contactinfo
			,	'canonical'                => $catXref[$key]
			,	'registration'             => 0
			);
			$table = JTable::getInstance('Event', 'JCalProTable');
			if (!$table->bind($data)) {
				$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_BIND_ERROR', JText::_('COM_JCALPRO_EVENT'), $data['title'], $table->getError()));
				return false;
			}
			if (!$table->check()) {
				$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_CHECK_ERROR', JText::_('COM_JCALPRO_EVENT'), $data['title'], $table->getError()));
				return false;
			}
			if (!$table->store()) {
				$this->setError(JText::sprintf('COM_JCALPRO_MIGRATION_STORE_ERROR', JText::_('COM_JCALPRO_EVENT'), $data['title'], $table->getError()));
				return false;
			}
		}
		// return an ok :)
		return array('ok' => JText::_('COM_JCALPRO_MIGRATION_COMPLETE'));
	}
	
	private function _installSampleData() {
		if ($this->checkCategories()) {
			$this->setError(JText::_('COM_JCALPRO_SAMPLEDATA_ALREADY_HAS_DATA'));
			return false;
		}
		$db = JFactory::getDbo();
		// start with fields
		$fields = array(
			array(
				'name'          => 'email'
			,	'title'         => JText::_('COM_JCALPRO_SAMPLEDATA_FIELD_EMAIL_TITLE')
			,	'type'          => 'email'
			,	'description'   => JText::_('COM_JCALPRO_SAMPLEDATA_FIELD_EMAIL_DESCRIPTION')
			,	'published'     => 1
			,	'formtype'      => 0
			,	'event_display' => 1
			,	'params'        => '{"classname":"inputbox","attrs":{"required":"true"}}'
			)
		,	array(
				'name'          => 'url'
			,	'title'         => JText::_('COM_JCALPRO_SAMPLEDATA_FIELD_URL_TITLE')
			,	'type'          => 'url'
			,	'description'   => JText::_('COM_JCALPRO_SAMPLEDATA_FIELD_URL_DESCRIPTION')
			,	'published'     => 1
			,	'formtype'      => 0
			,	'event_display' => 1
			,	'params'        => '{"classname":"inputbox"}'
			)
		,	array(
				'name'          => 'list'
			,	'title'         => JText::_('COM_JCALPRO_SAMPLEDATA_FIELD_LIST_TITLE')
			,	'type'          => 'list'
			,	'description'   => JText::_('COM_JCALPRO_SAMPLEDATA_FIELD_LIST_DESCRIPTION')
			,	'published'     => 1
			,	'formtype'      => 0
			,	'event_display' => 1
			,	'params'        => '{"classname":"inputbox","opts":{"Option A":"A","Option B":"B","Option C":"C","Option D":"D","Option E":"E"},"attrs":{"multiple":"true","required":"true","size":"3"}}'
			)
		,	array(
				'name'          => 'integer'
			,	'title'         => JText::_('COM_JCALPRO_SAMPLEDATA_FIELD_INTEGER_TITLE')
			,	'type'          => 'integer'
			,	'description'   => JText::_('COM_JCALPRO_SAMPLEDATA_FIELD_INTEGER_DESCRIPTION')
			,	'published'     => 1
			,	'formtype'      => 0
			,	'event_display' => 1
			,	'params'        => '{"classname":"inputbox","attrs":{"first":"1","last":"10","step":"1"}}'
			)
		,	array(
				'name'          => 'tel'
			,	'title'         => JText::_('COM_JCALPRO_SAMPLEDATA_FIELD_TEL_TITLE')
			,	'type'          => 'tel'
			,	'description'   => JText::_('COM_JCALPRO_SAMPLEDATA_FIELD_TEL_DESCRIPTION')
			,	'published'     => 1
			,	'formtype'      => 1
			,	'event_display' => 1
			,	'params'        => '{"classname":"inputbox"}'
			)
		);
		
		$efieldids = array();
		$rfieldids = array();
		foreach ($fields as $field) {
			$table = JTable::getInstance('Field', 'JCalProTable');
			if (!$table->bind($field)) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_BIND_ERROR', JText::_('COM_JCALPRO_FIELD'), $field['title'], $table->getError()));
				return false;
			}
			if (!$table->check()) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_CHECK_ERROR', JText::_('COM_JCALPRO_FIELD'), $field['title'], $table->getError()));
				return false;
			}
			if (!$table->store()) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_STORE_ERROR', JText::_('COM_JCALPRO_FIELD'), $field['title'], $table->getError()));
				return false;
			}
			switch ($field['formtype']) {
				case 0 : $efieldids[] = $table->id; break;
				case 1 : $rfieldids[] = $table->id; break;
				default:
					$efieldids[] = $table->id;
					$rfieldids[] = $table->id;
					break;
			}
		}
		// follow with forms
		$forms = array(
			array(
				'title'      => JText::_('COM_JCALPRO_SAMPLEDATA_FORM_EVENT_TITLE')
			,	'type'       => 0
			,	'published'  => 1
			,	'default'    => 1
			, 'formfields' => implode('|', $efieldids)
			)
		,	array(
				'title'      => JText::_('COM_JCALPRO_SAMPLEDATA_FORM_REGISTRATION_TITLE')
			,	'type'       => 1
			,	'published'  => 1
			,	'default'    => 1
			, 'formfields' => implode('|', $rfieldids)
			)
		);
		$formids = array(
			'event'        => ''
		,	'registration' => ''
		);
		foreach ($forms as $form) {
			$table = JTable::getInstance('Form', 'JCalProTable');
			if (!$table->bind($form)) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_BIND_ERROR', JText::_('COM_JCALPRO_FORM'), $form['title'], $table->getError()));
				return false;
			}
			if (!$table->check()) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_CHECK_ERROR', JText::_('COM_JCALPRO_FORM'), $form['title'], $table->getError()));
				return false;
			}
			if (!$table->store()) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_STORE_ERROR', JText::_('COM_JCALPRO_FORM'), $form['title'], $table->getError()));
				return false;
			}
			$formids[0 == $form['type'] ? 'event' : 'registration'] = $table->id;
		}
		// before we save the categories, we need to pull the asset data for the main component
		$rules = $this->_getRules();
		// create new categories using JTable
		$category = array(
			array(
				'title'       => JText::_('COM_JCALPRO_SAMPLEDATA_CATEGORY_TITLE')
			,	'description' => JText::_('COM_JCALPRO_SAMPLEDATA_CATEGORY_DESCRIPTION')
			,	'extension'   => 'com_jcalpro'
			,	'parent_id'   => 1
			,	'published'   => 1
			,	'access'      => 1
			,	'language'    => '*'
			,	'rules'       => $rules
			,	'params'      => array(
					'jcalpro_color'            => 'FF0000'
				,	'jcalpro_eventform'        => $formids['event']
				,	'jcalpro_registrationform' => $formids['registration']
				)
			)
		,	array(
				'title'       => JText::_('COM_JCALPRO_SAMPLEDATA_CATEGORY_TITLE2')
			,	'description' => JText::_('COM_JCALPRO_SAMPLEDATA_CATEGORY_DESCRIPTION2')
			,	'extension'   => 'com_jcalpro'
			,	'parent_id'   => 1
			,	'published'   => 1
			,	'access'      => 1
			,	'language'    => '*'
			,	'rules'       => $rules
			,	'params' => array(
					'jcalpro_color'            => 'FFFFFF'
				,	'jcalpro_eventform'        => $formids['event']
				,	'jcalpro_registrationform' => $formids['registration']
				)
			)
		);
		$catids = array();
		foreach ($category as $cat) {
			$table = JTable::getInstance('Category');
			if (!$table->bind($cat)) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_BIND_ERROR', JText::_('COM_JCALPRO_CATEGORY'), $cat['title'], $table->getError()));
				return false;
			}
			if (!$table->check()) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_CHECK_ERROR', JText::_('COM_JCALPRO_CATEGORY'), $cat['title'], $table->getError()));
				return false;
			}
			if (!$table->store()) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_STORE_ERROR', JText::_('COM_JCALPRO_CATEGORY'), $cat['title'], $table->getError()));
				return false;
			}
			$table->moveByReference(0, 'last-child', $table->id);
			$catids[] = $table->id;
		}
		
		// end with events with dates based on "today"
		$today  = JCalProHelperDate::getToday()->toJoomla()->toHourStart();
		$base   = clone $today;
		$email  = JFactory::getConfig()->get('mailfrom');
		$url    = JUri::root();
		// set base
		// start with 2 days after today at 2pm
		$base->addDay(2)->toHour(14);
		
		$events = array(
			array(
				'title'              => JText::_('COM_JCALPRO_SAMPLEDATA_EVENT_NORMAL_TITLE')
			,	'description'        => JText::_('COM_JCALPRO_SAMPLEDATA_EVENT_NORMAL_DESCRIPTION')
			,	'rec_id'             => 0
			,	'detached_from_rec'  => 0
			,	'day'                => $base->day()
			,	'month'              => $base->month()
			,	'year'               => $base->year()
			,	'hour'               => $base->hour()
			,	'minute'             => $base->minute()
			,	'timezone'           => $base->timezone()
			,	'start_date'         => $base->toSql()
			// this event lasts 2 hours
			,	'end_date'           => $base->addHour(2)->toSql()
			,	'recur_type'         => 0
			,	'recur_end_type'     => 1
			,	'recur_end_count'    => 2
			,	'recur_end_until'    => ''
			,	'approved'           => 1
			,	'published'          => 1
			,	'duration_type'      => 1
			,	'end_days'           => 0
			,	'end_hours'          => 2
			,	'end_minutes'        => 0
			,	'params'             => array('email' => $email, 'url' => $url, 'list' => array('C', 'D'))
			,	'canonical'          => $catids[0]
			,	'registration'       => 1
			,	'registration_capacity'     => 200
			,	'registration_start_day'    => $today->day()
			,	'registration_start_month'  => $today->month()
			,	'registration_start_year'   => $today->year()
			,	'registration_start_hour'   => $today->hour()
			,	'registration_start_minute' => $today->minute()
			,	'registration_until_event'  => 1
			)
		,	array(
				'title'              => JText::_('COM_JCALPRO_SAMPLEDATA_EVENT_REPEAT_TITLE')
			,	'description'        => JText::_('COM_JCALPRO_SAMPLEDATA_EVENT_REPEAT_DESCRIPTION')
			,	'rec_id'             => 0
			,	'detached_from_rec'  => 0
			// here we increase the event by 3 days and set it to noon
			,	'day'                => $base->addDay(3)->toHour(12)->day()
			,	'month'              => $base->month()
			,	'year'               => $base->year()
			,	'hour'               => $base->hour()
			,	'minute'             => $base->minute()
			,	'timezone'           => $base->timezone()
			,	'start_date'         => $base->toSql()
			// this event lasts 2 hours
			,	'end_date'           => $base->addHour(2)->toSql()
			// repeating daily event
			,	'recur_type'         => 1
			,	'rec_daily_period'   => 1
			,	'recur_end_type'     => 1
			,	'recur_end_count'    => 3
			,	'recur_end_until'    => ''
			,	'approved'           => 1
			,	'published'          => 1
			,	'duration_type'      => 2
			,	'end_days'           => 0
			,	'end_hours'          => 1
			,	'end_minutes'        => 0
			,	'params'             => array('email' => $email, 'url' => $url, 'list' => array('A', 'B'))
			,	'canonical'          => $catids[1]
			,	'registration'       => 0
			)
		);
		$eventids = array();
		foreach ($events as $event) {
			$table = JTable::getInstance('Event', 'JCalProTable');
			if (!$table->bind($event)) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_BIND_ERROR', JText::_('COM_JCALPRO_EVENT'), $event['title'], $table->getError()));
				return false;
			}
			if (!$table->check()) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_CHECK_ERROR', JText::_('COM_JCALPRO_EVENT'), $event['title'], $table->getError()));
				return false;
			}
			if (!$table->store()) {
				$this->setError(JText::sprintf('COM_JCALPRO_SAMPLEDATA_STORE_ERROR', JText::_('COM_JCALPRO_EVENT'), $event['title'], $table->getError()));
				return false;
			}
			$eventids[] = $table->id;
		}
		// extra fix for approval
		$db->setQuery((string) $db->getQuery(true)
			->update('#__jcalpro_events')
			->set($db->quoteName('approved') . ' = 1')
			->where('(' . $db->quoteName('id') . ' IN (' . implode(',', $eventids) . ') OR ' . $db->quoteName('rec_id') . ' IN (' . implode(',', $eventids) . '))')
		);
		$db->query();
		// return an ok :)
		return array('ok' => JText::_('COM_JCALPRO_SAMPLEDATA_INSTALLED'));
	}
	
	private function _getRules() {
		static $rules;
		if (is_null($rules)) {
			$db = JFactory::getDbo();
			$db->setQuery((string) $db->getQuery(true)
					->select('rules')
					->from('#__assets')
					->where($db->quoteName('name') . ' = ' . $db->Quote('com_jcalpro'))
			);
			$rules = $db->loadResult();
			$registry = new JRegistry();
			$registry->loadString($rules);
			$rules = $registry->toArray();
			$keys = array("core.create","core.delete","core.edit","core.edit.state","core.edit.own");
			foreach (array_keys($rules) as $key) {
				if (!in_array($key, $keys)) {
					unset($rules[$key]);
					continue;
				}
				if ("core.edit.state" == $key) {
					$rules["core.moderate"] = $rules[$key];
				}
				if ("core.create" == $key) {
					$rules["core.create.private"] = $rules[$key];
				}
			}
		}
		return $rules;
	}
}
