<?php
/**
 * @version		$Id: log.php 811 2012-10-09 15:51:00Z jeffchannell $
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

jimport('joomla.log.log');
jimport('jcaldate.date');

JLoader::register('JCalPro', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/jcalpro.php');

abstract class JCalProHelperLog
{
	/**
	 * Debugging message specific to JCalPro
	 * @var int
	 */
	const DEBUG = 256;
	
	static public function setup() {
		static $log;
		if (is_null($log)) {
			$today = JCalDate::_()->toRequest();
			$logs = array(
				'debug' => JCalProHelperLog::DEBUG
			,	'info'  => JLog::INFO
			,	'error' => JLog::ERROR
			);
			foreach ($logs as $key => $mask) {
				JLog::addLogger(array('text_file' => "jcalpro.$key.$today.php"), $mask);
			}
			$log = true;
		}
	}
	
	static public function debug($message, $category = 'jcalpro') {
		if (JDEBUG) self::_log($message, JCalProHelperLog::DEBUG, $category, 'debug');
	}
	
	static public function log($message, $category = 'jcalpro') {
		self::_log($message, JLog::INFO, $category, 'info');
	}
	
	static public function error($message, $category = 'jcalpro') {
		self::_log($message, JLog::ERROR, $category, 'error');
	}
	
	static public function errorMessage($message, $category = 'jcalpro') {
		JFactory::getApplication()->enqueueMessage(print_r($message, 1), 'error');
		self::error($message, $category);
	}
	
	static public function logMessage($message, $category = 'jcalpro') {
		JFactory::getApplication()->enqueueMessage(print_r($message, 1), 'message');
		self::log($message, $category);
	}
	
	static public function debugMessage($message, $category = 'jcalpro') {
		JFactory::getApplication()->enqueueMessage(print_r($message, 1), 'info');
		self::debug($message, $category);
	}
	
	static public function toss($message, $category = 'jcalpro') {
		$e = new Exception($message);
		self::error(JText::sprintf('COM_JCALPRO_EXCEPTION_LOGGED', $message, $e->getTraceAsString()), $category);
		throw $e;
	}
	
	static private function _log($message, $priority = JLog::INFO, $category = 'jcalpro') {
		self::setup();
		JLog::add(print_r($message, 1), $priority, $category);
	}
}
