<?php
/**
 * @version		$Id: jcalpro.php 812 2012-10-09 18:56:47Z jeffchannell $
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

abstract class JCalPro
{
	const COM = 'com_jcalpro';
	
	const RECUR_TYPE_NONE    = 0;
	const RECUR_TYPE_DAILY   = 1;
	const RECUR_TYPE_WEEKLY  = 2;
	const RECUR_TYPE_MONTHLY = 3;
	const RECUR_TYPE_YEARLY  = 4;
	
	const JCL_RECUR_NO_LIMIT = 0;
	
	const JCL_SHOW_RECURRING_EVENTS_NONE          = 0;
	const JCL_SHOW_RECURRING_EVENTS_ALL           = 1;
	const JCL_SHOW_RECURRING_EVENTS_FIRST_ONLY    = 2;
	const JCL_SHOW_RECURRING_EVENTS_NEXT_ONLY     = 2;
	const JCL_SHOW_RECURRING_EVENTS_DEFER_TO_JCAL = 3;
	
	const JCL_EVENT_DURATION_NONE = 0;
	const JCL_EVENT_DURATION_DATE = 1;
	const JCL_EVENT_DURATION_ALL = 2;

	const JCL_EVENT_NO_END_DATE = '0000-00-00 00:00:00';

	const JCL_ALL_DAY_EVENT_END_DATE_LEGACY   = '0000-00-00 00:00:01';
	const JCL_ALL_DAY_EVENT_END_DATE_LEGACY_2 = '9999-12-01 00:00:00';
	const JCL_ALL_DAY_EVENT_END_DATE          = '2038-01-18 00:00:00';
	
	private static $_actions = array('core.admin', 'core.manage', 'core.create', 'core.create.private', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete', 'core.moderate');

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @return	JObject
	 */
	public static function getActions($categoryId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
		
		$assetName = self::COM . (empty($categoryId) ? '' : '.category.'.(int) $categoryId);

		foreach (self::$_actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
	
	/**
	 * determines if the given (or current) user can add new events
	 * 
	 * @param mixed  $catid
	 * @param mixed  $uid
	 */
	public static function canAddEvents($catid = null, $uid = null) {
		$user = JFactory::getUser($uid);
		return (self::canDo('core.create', $catid, $uid) || (self::canDo('core.create.private', $catid, $uid) && $user->id));
	}
	
	/**
	 * determines if the given (or current) user can moderate events
	 * 
	 * @param mixed  $catid
	 * @param mixed  $uid
	 */
	public static function canModerateEvents($catid = null, $uid = null) {
		return self::canDo('core.moderate', $catid, $uid);
	}
	
	/**
	 * determines if the given (or current) user can change events states
	 * 
	 * @param mixed  $catid
	 * @param mixed  $uid
	 */
	public static function canPublishEvents($catid = null, $uid = null) {
		return self::canDo('core.edit.state', $catid, $uid);
	}
	
	/**
	 * determines if the given (or current) user can delete events
	 * 
	 * @param mixed  $catid
	 * @param mixed  $uid
	 */
	public static function canDeleteEvents($catid = null, $uid = null) {
		return self::canDo('core.delete', $catid, $uid);
	}
	
	/**
	 * determines if the given (or current) user can perform the given action(s)
	 * 
	 * @param string $action
	 * @param mixed  $catid
	 * @param mixed  $uid
	 */
	public static function canDo($action, $catid = null, $uid = null) {
		// set the catids array
		$catids = array();
		// some variables
		$user  = JFactory::getUser($uid);
		$app   = JFactory::getApplication();
		// special case - guests CAN NOT create private events!
		if ('core.create.private' == $action && empty($user->id)) return false;
		// we need to know if the user has already selected a catid
		// because if they have, we can just check that category
		// if not, we have to check all the categories
		/*
		if (is_null($catid)) {
			$catid = $app->getUserStateFromRequest(self::COM . '.events.jcal.catid', 'catid', 0);
		}
		*/
		if ($catid) {
			// add to the stack
			$catids[] = $catid;
		}
		// get all the available categories using the events model and check each
		else {
			JLoader::register('JCalProBaseModel', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/models/basemodel.php');
			JCalProBaseModel::addIncludePath(($app->isAdmin() ? JPATH_ADMINISTRATOR : JPATH_ROOT) . '/components/' . self::COM . '/models');
			$eventsModel = self::getModelInstance('Events', 'JCalProModel');
			$eventsModel->setState('filter.published', '1');
			$categories = $eventsModel->getCategories();
			if (!empty($categories)) {
				foreach ($categories as $cat) {
					$catids[] = $cat->id;
				}
			}
		}
		// if there are no categories, just check root
		/*
		if (empty($catids)) {
			return $user->authorise($action, self::COM);
		}
		*/
		JArrayHelper::toInteger($catids);
		
		// start checking if we can add
		if (!empty($catids)) {
			foreach ($catids as $cid) {
				$addToCat = $user->authorise($action, self::COM . '.category.' . $cid);
				// great, we can add in one of the categories
				if ($addToCat) return true;
			}
		}
		
		// if we got this far, we can't add
		return false;
	}
	
	/**
	 * gets a SINGULAR instance of a model
	 * 
	 * @param  string $type
	 * @param  string $prefix
	 * @param  array  $config
	 * @return mixed
	 */
	public static function getModelInstance($type, $prefix = 'JCalProModel', $config = array()) {
		// we only want one instance per key
		static $models;
		// instantiate our static array
		if (!is_array($models)) {
			JLoader::register('JCalProBaseModel', JPATH_ADMINISTRATOR . '/components/com_jcalpro/libraries/models/basemodel.php');
			$models = array();
		}
		// get our key
		$key = md5($type . $prefix . serialize($config));
		//JCalProHelperLog::debug(JText::sprintf('COM_JCALPRO_GETTING_MODEL_INSTANCE', $prefix . $type, $key));
		if (!array_key_exists($key, $models)) {
			//JCalProHelperLog::debug(JText::sprintf('COM_JCALPRO_MODEL_INSTANCE_NOT_FOUND', $prefix . $type, $key));
			$models[$key] = JCalProBaseModel::getInstance($type, $prefix, $config);
		}
		// send back our model
		return $models[$key];
	}
	
	/**
	 * static method to get either the component parameters,
	 * or when a key is supplied the value of that key
	 * if val is supplied (with a key) def() is used instead of get()
	 * 
	 * @param  string $key
	 * @param  mixed  $val
	 * @return mixed
	 */
	public static function config($key = null, $val = null) {
		static $params;
		if (!isset($params)) {
			$app = JFactory::getApplication();
			// get the params, either from the helper or the application
			if ($app->isAdmin() || 'com_jcalpro' != $app->input->get('option', '', 'cmd')) {
				$params = JComponentHelper::getParams(self::COM);
			} else {
				$params = $app->getParams();
			}
		}
		// if we don't have a key, return the entire params object
		if (is_null($key) || empty($key)) {
			return $params;
		}
		// return the param value, with optional def
		if (is_null($val)) return $params->get($key);
		return $params->def($key, $val);
	}
	
	/**
	 * loads language files, english first then configured language
	 * 
	 * @param string $name
	 * @param mixed  $client
	 */
	public static function language($name, $client = null) {
		// force client
		if (is_null($client) || !is_string($client)) $client = JPATH_ROOT;
		// we really only want to load once each asset
		static $langs;
		// initialize our static list
		if (!is_array($langs)) $langs = array();
		// create our key
		$key = md5($name . $client);
		// set the list item if it's not been set
		if (!array_key_exists($key, $langs)) {
			// what language should we try?
			$user  = JFactory::getUser();
			$ulang = $user->getParam('language', $user->getParam('admin_language'));
			$lang  = JFactory::getLanguage();
			$langs[$key] = $lang->load($name, $client, $ulang, true) || $lang->load($name, $client, 'en-GB');
		}
		// return the value :)
		return $langs[$key];
	}
	
	/*
	public static function allowAccessBypass() {
		static $allowed;
		if (is_null($allowed)) {
			$allowed = array();
			$token = JFactory::getApplication()->input->get('token', '', 'cmd');
			if (!empty($token)) {
				$db = JFactory::getDbo();
				$db->setQuery((string) $db->getQuery(true)
					->select('id')
					->from('#__users')
					->where('MD5(CONCAT_WS("::", ))' . $db->Quote($token))
				);
			}
		}
		return $allowed;
	}
	*/
	
	
	/**
	 * adds Google maps scripts to the document
	 * 
	 * @param bool  $component
	 * @param array $libraries
	 */
	public static function mapScript($component = true, $libraries = array()) {
		JLoader::register('JCalProHelperUrl', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/url.php');
		JHtml::_('behavior.framework');
		$document = JFactory::getDocument();
		if (method_exists($document, 'addScript')) {
			JText::script('COM_JCALPRO_GEOCODER_STATUS_INVALID_REQUEST');
			JText::script('COM_JCALPRO_GEOCODER_STATUS_OVER_QUERY_LIMIT');
			JText::script('COM_JCALPRO_GEOCODER_STATUS_REQUEST_DENIED');
			JText::script('COM_JCALPRO_GEOCODER_STATUS_ZERO_RESULTS');
			$script = '//maps.googleapis.com/maps/api/js?sensor=false';
			if (!empty($libraries)) $script .= '&libraries=' . implode(',', $libraries);
			$document->addScript($script);
			if ($component) {
				$document->addScript(JCalProHelperUrl::media() . '/js/jcalpro.js');
				$document->addScript(JCalProHelperUrl::media() . '/js/map.js');
				$document->addStyleSheet(JCalProHelperUrl::media() . '/css/map.css');
			}
		}
	}
	/*@/jcal_standard_code@*/
	
	/**
	 * static method to keep track of debug info
	 * 
	 * @param  string  $name
	 * @param  mixed   $data
	 * @return array
	 */
	public static function debugger($name = null, $data = null) {
		static $debug;
		if (!is_array($debug)) $debug = array();
		if (!is_null($name)) $debug[$name] = $data;
		return $debug;
	}
	
	/**
	 * static method to aide in debugging
	 * 
	 * @param  mixed  $data
	 * @param  string $fin
	 * @return mixed
	 */
	public static function debug($data, $fin = 'echo') {
		if (!JDEBUG) return '';
		$e       = new Exception;
		$output  = "<pre>\n" . htmlspecialchars(print_r($data, 1)) . "\n\n" . $e->getTraceAsString() . "\n</pre>\n";
		switch ($fin) {
			case 'return': return $output;
			case 'die'   : echo $output; die();
			case 'echo'  :
			default      :
				echo $output; return;
		}
	}
	
	/**
	 * gets an instance of JVersion
	 * 
	 */
	public static function version() {
		static $version;
		if (is_null($version)) {
			$version = new JVersion;
		}
		return $version;
	}
}
