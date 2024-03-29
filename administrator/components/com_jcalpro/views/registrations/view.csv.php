<?php
/**
 * @version		$Id: view.csv.php 817 2012-10-16 17:06:43Z jeffchannell $
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
jimport('jcaldate.date');

class JCalProViewRegistrations extends JCalProView
{
	function display($tpl = null) {
		$date     = new JCalDate();
		$basename = 'event-registrations-' . $date->toRequest();
		$filetype = 'csv';
		$mimetype = 'text/csv';
		$content  = $this->get('Content');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$document = JFactory::getDocument();
		$document->setMimeEncoding($mimetype);
		header('Content-disposition:attachment; filename="'.$basename.'.'.$filetype.'"; creation-date="'.$date->toRFC822().'"');
		echo $content;
		// we need to exit here so plugins don't add arbitrary crap to the file
		die;
	}
}
