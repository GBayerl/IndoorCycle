<?php
/**
 * @version		$Id: field.php 785 2012-05-07 22:37:46Z jeffchannell $
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

JLoader::register('JCalProAdminModel', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/models/basemodeladmin.php');

/**
 * This models supports retrieving lists of fields.
 *
 * @package		JCalPro
 * @subpackage	com_jcalpro
 */
class JCalProModelField extends JCalProAdminModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_jcalpro.field';
	
	/**
	 * The event to trigger after saving the data.
	 * 
	 * @var    string
	 */
	protected $event_after_save = 'onJCalAfterSave';
	
	/**
	 * The event to trigger before saving the data.
	 * 
	 * @var    string
	 */
	protected $event_before_save = 'onJCalBeforeSave';
	
	public function getTable($type='Field', $prefix='JCalProTable', $config=array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data=array(), $loadData=true) {
		return parent::getForm($data, $loadData);
	}
}
