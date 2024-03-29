<?php
/**
 * @version		$Id: loader.php 801 2012-09-07 15:44:13Z jeffchannell $
 * @package		JCalPro
 * @subpackage	lib_phpqrcode

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

defined('_JEXEC') or die;

// ensure we have logs
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

$qrlogs = JFactory::getConfig()->get('log_path') . '/qrconfig';
if (!JFolder::exists($qrlogs)) {
	JFolder::create($qrlogs);
}

$qrerrors = $qrlogs . '/errors.txt';
if (!JFile::exists($qrerrors)) {
	JFile::write('', $qrerrors);
}

require_once dirname(__FILE__) . '/phpqrcode/qrlib.php';