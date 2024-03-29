<?php
/**
 * @version		$Id: registrations.php 822 2012-10-22 23:06:00Z jeffchannell $
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

jimport('joomla.application.component.controlleradmin');

class JCalProControllerRegistrations extends JControllerAdmin
{
	public function getModel($name='Registration', $prefix = 'JCalProModel') {
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
	
	public function publish() {
		parent::publish();
		$input = JFactory::getApplication()->input;
		$task  = $input->get('task', 'registration.publish');
		$cid   = $input->get('cid', array(), 'array');
		$db    = JFactory::getDbo();
		//if ('registration.publish' !== $task) return;
		
		if (is_array($cid) && !empty($cid)) {
			JArrayHelper::toInteger($cid);
			$db->setQuery((string) $db->getQuery(true)
				->update('#__jcalpro_registration')
				->set($db->quoteName('confirmation') . ' = ' . $db->quote(''))
				->where($db->quoteName('id') . ' IN (' . implode(',', $cid) . ')')
			);
			try {
				$db->query();
			}
			catch (Exception $e) {
				
			}
		}
	}
	
	public function export() {
		JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_jcalpro&view=registrations&format=csv', false));
		jexit();
	}
}
