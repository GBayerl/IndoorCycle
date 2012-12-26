<?php
/**
 * @version		$Id: locations.php 799 2012-08-08 22:56:32Z jeffchannell $
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

JLoader::register('JCalProLocationsController', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/controllers/basecontrollerlocations.php');
JLoader::register('JCalProHelperUrl', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/url.php');

class JCalProControllerLocations extends JCalProLocationsController
{
	public function saverules() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		if (!JFactory::getUser()->authorise('core.admin')) {
			JError::raiseError(403, JText::_('COM_JCALPRO_LOCATIONS_PERMISSIONS_SAVE_NOT_AUTH'));
			jexit();
		}
		$app   = JFactory::getApplication();
		$url   = JCalProHelperUrl::view('locations', false);
		$msg   = JText::_('COM_JCALPRO_LOCATIONS_PERMISSIONS_SAVE_SUCCESS');
		$type  = 'message';
		$rules = $app->input->post->get('rules', array(), 'array');
		// if we have no rules, then there's something amiss
		if (empty($rules)) {
			$msg  = JText::_('COM_JCALPRO_LOCATIONS_PERMISSIONS_NO_RULES');
			$type = 'error';
		}
		// save our rules to the assets table
		else {
			$saferules = array();
			// sanitize the rules
			foreach ($rules as $action => $identities) {
				if (!array_key_exists($action, $saferules)) $saferules[$action] = array();
				if (!empty($identities)) {
					foreach ($identities as $group => $permission) {
						if ('' == $permission) continue;
						$saferules[$action][$group] = (int) ((bool) $permission);
					}
				}
			}
			// get our dbo
			$db = JFactory::getDbo();
			// find our parent asset
			$db->setQuery($db->getQuery(true)
				->select('id, level')
				->from('#__assets')
				->where('name = ' . $db->Quote('com_jcalpro'))
			);
			$parent = $db->loadObject();
			// find our asset
			$db->setQuery($db->getQuery(true)
				->select('id')
				->from('#__assets')
				->where('name = ' . $db->Quote('com_jcalpro.locations'))
			);
			$asset_id = (int) $db->loadResult();
			// create our bind data
			$bind = array(
				'rules' => json_encode($saferules)
			,	'name' => 'com_jcalpro.locations'
			,	'title' => JText::_('COM_JCALPRO_LOCATIONS_PERMISSIONS')
			,	'level' => ((int) $parent->level) + 1
			,	'parent_id' => $parent->id
			,	'id' => $asset_id
			);
			// now get an instance of the asset table
			$asset = JTable::getInstance('Asset');
			// save our asset
			if (!$asset->bind($bind)) {
				$msg  = JText::_('COM_JCALPRO_LOCATIONS_PERMISSIONS_ERROR_BIND');
				$type = 'error';
			}
			else {
				if (!$asset->check()) {
					$msg  = JText::_('COM_JCALPRO_LOCATIONS_PERMISSIONS_ERROR_CHECK');
					$type = 'error';
				}
				else {
					if (!$asset->store()) {
						$msg  = JText::_('COM_JCALPRO_LOCATIONS_PERMISSIONS_ERROR_STORE');
						$type = 'error';
					}
				}
			}
		}
		$app->redirect($url, $msg, $type);
		jexit();
	}
}
