<?php
/**
 * @version		$Id: jcalpro.php 799 2012-08-08 22:56:32Z jeffchannell $
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

jimport('joomla.application.component.controller');
jimport('joomla.error.profiler');

$profiler = JProfiler::getInstance('Application');
$profiler->mark('onJCalProStart');

JLoader::register('JCalProHelperLog', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/log.php');
JCalProHelperLog::setup();

// ensure we have the correct application - 2.5 or 3.x
if (jimport('joomla.application.component.controller')) {
	$controller = JController::getInstance('JCalPro');
}
// ouch, 3.x - over time we'll want to migrate to this method!
else {
	jimport('legacy.controllers.legacy');
	$controller = JControllerLegacy::getInstance('JCalPro');
}

$controller->execute(JFactory::getApplication()->input->get('task', '', 'cmd'));
$controller->redirect();

$profiler->mark('onJCalProEnd');