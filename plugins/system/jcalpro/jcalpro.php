<?php
/**
 * @version		$Id: jcalpro.php 830 2012-10-25 20:52:20Z jeffchannell $
 * @package		JCalPro
 * @subpackage	plg_system_jcalpro

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

jimport('joomla.filesystem.file');
jimport('joomla.plugin.plugin');
// we HAVE to force-load the helper here to prevent fatal errors!
$helper = JPATH_ADMINISTRATOR . '/components/com_jcalpro/helpers/jcalpro.php';
if (JFile::exists($helper)) require_once $helper;

class plgSystemJCalPro extends JPlugin
{
	public static $com = 'com_jcalpro';
	/**
	 * Constructor
	 * 
	 * @param unknown_type $subject
	 * @param unknown_type $config
	 */
	public function __construct(&$subject, $config) {
		// if something happens & the helper class can't be found, we don't want a fatal error here
		if (class_exists('JCalPro')) {
			JCalPro::language(self::$com, JPATH_ADMINISTRATOR);
		}
		else {
			$this->loadLanguage();
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_JCALPRO_COMPONENT_NOT_INSTALLED'));
		}
		parent::__construct($subject, $config);
	}
	
	public function loadLanguage($extension = 'plg_system_jcalpro.sys', $basePath = JPATH_ADMINISTRATOR) {
		parent::loadLanguage($extension, $basePath);
	}
	
	public function onAfterInitialise() {
		// for debugging only :)
		if (defined('JDEBUG') && JDEBUG) {
			try {
				jimport('jcaldate.date');
				$time = new JCalDate();
				$this->loadLanguage();
				JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_SYSTEM_JCALPRO_DEBUG_TIMES', $time->toSql(), $time->toJoomla()->toSql(), $time->timezone(), $time->toUser()->toSql(), $time->timezone()));
			}
			catch (Exception $e) {
				return;
			}
		}
	}
	
	/**
	 * onAfterDispatch
	 * 
	 * handles flair after dispatch
	 */
	public function onAfterDispatch() {
		$app = JFactory::getApplication();
		// we want to add some extras to com_categories
		if ($app->isAdmin() && 'com_categories' == $app->input->get('option', '', 'cmd') && self::$com == $app->input->get('extension', '', 'cmd') && class_exists('JCalPro')) {
			// UPDATE: don't do this in edit layout in 3.0+
			if (JCalPro::version()->isCompatible('3.0') && 'edit' == $app->input->get('layout')) return;
			// add submenu to categories
			JLoader::register('JCalProView', JPATH_ADMINISTRATOR . '/components/' . self::$com . '/libraries/views/baseview.php');
			$comView = new JCalProView();
			$comView->addMenuBar();
			// add script to inject extra columns into the categories list table
			JLoader::register('JCalProHelperUrl', JPATH_ADMINISTRATOR . '/components/' . self::$com . '/helpers/url.php');
			JText::script('COM_JCALPRO_TOTAL_EVENTS');
			JText::script('COM_JCALPRO_UPCOMING_EVENTS');
			JFactory::getDocument()->addScript(JCalProHelperUrl::media() . '/js/jcalpro.js');
			JFactory::getDocument()->addScript(JCalProHelperUrl::media() . '/js/categories.js');
		}
	}
}
