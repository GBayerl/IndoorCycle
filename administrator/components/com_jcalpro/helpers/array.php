<?php
/**
 * @version		$Id: array.php 835 2012-11-26 23:39:14Z jeffchannell $
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

abstract class JCalProHelperArray
{
	static public function merge() {
		$arrays = func_get_args();
		$base   = array_shift($arrays);
		
		if (!is_array($base)) $base = empty($base) ? array() : array($base);
		
		foreach ($arrays as $append) {
			if (!is_array($append)) $append = array($append);
			foreach ($append as $key => $value) {
				if (!array_key_exists($key, $base) && !is_numeric($key)) {
					$base[$key] = $append[$key];
					continue;
				}
				if (is_array($value) || is_array($base[$key])) {
					$base[$key] = self::merge($base[$key], $append[$key]);
				}
				else if (is_numeric($key)) {
					if (!in_array($value, $base)) $base[] = $value;
				}
				else {
					$base[$key] = $value;
				}
			}
		}
		
		return $base;
	}
}