<?php
/**
 * @version		$Id: view.html.php 805 2012-09-20 01:26:01Z jeffchannell $
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

JLoader::register('JCalProView', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/views/baseview.php');
JLoader::register('JCalPro', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/jcalpro.php');
JLoader::register('JCalProHelperUrl', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/url.php');

class JCalProViewInstall extends JCalProView
{
	function display($tpl = null, $echo = true) {
		// add our html helper
		jimport('joomla.html.html');
		JHtml::addIncludePath(JPATH_ROOT.'/components/com_jcalpro/helpers/html');
		// do we show the inputs?
		$this->showSampleDataButton = !$this->getModel()->checkCategories();
		$this->showMigrationButton  = $this->getModel()->checkMigration();
		// load our component info
		$manifest = JPATH_ADMINISTRATOR . '/components/com_jcalpro/jcalpro.xml';
		jimport('joomla.installer.installer');
		if (class_exists('JInstaller') && method_exists('JInstaller', 'parseXMLInstallFile')) {
			$this->details = JInstaller::parseXMLInstallFile($manifest);
		}
		else {
			jimport('joomla.application.helper');
			$this->details = JApplicationHelper::parseXMLInstallFile($manifest);
		}
		// display
		parent::display($tpl);
	}
}
