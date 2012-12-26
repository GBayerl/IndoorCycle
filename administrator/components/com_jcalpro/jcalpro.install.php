<?php
/**
 * @version		$Id: jcalpro.install.php 835 2012-11-26 23:39:14Z jeffchannell $
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

class com_JCalProInstallerScript extends ComJCalProStandardInstallationLibrary
{
	public $parent = null;
	
	public function __construct() {
		$this->_jc_extension		= 'com_jcalpro';
	}
}

/**
 * JCalPro Standard Installation Library
 * 
 * thanks to Nick D. @ Akeeba!
 */
class ComJCalProStandardInstallationLibrary {
	
	protected $_jc_extension = '';
	
	protected $_jc_uninstall;
	
	protected $_jc_categories;
	
	protected $_jc_assets;
	
	/**
	 * Joomla! pre-flight event
	 * 
	 * @param string $type Installation type (install, update, discover_install)
	 * @param JInstaller $parent Parent object
	 */
	public function preflight($type, $parent) {
		// Joomla! 1.6/1.7 bugfix for "Can not build admin menus"
		if (in_array($type, array('install', 'discover_install'))) {
			$this->_bugfixDBFunctionReturnedNoError();
		}
		else {
			$this->_bugfixCantBuildAdminMenus();
		}
		/*
		// When we're uninstalling, we need to ensure data stays if desired
		// however, by the time "uninstall" runs I *think* the config data will be gone
		if ('uninstall' == $type) {
			$app = JFactory::getApplication();
			// go ahead and load the helper, if we can...
			JLoader::register(JPATH_ADMINISTRATOR . '/components/com_jcalpro/helpers/jcalpro.php');
			try {
				$this->_jc_uninstall = (bool) JCalPro::config('uninstall_database');
				// if we're not uninstalling the data, we have to grab the categories and assets
				if (!$this->_jc_uninstall) {
					$db = JFactory::getDbo();
					// load the category data
					$db->setQuery((string) $db->getQuery(true)
						->select('*')
						->from($db->quoteName('#__categories'))
						->where($db->quoteName('extension') . ' = ' . $db->Quote($this->_jc_extension))
					);
					$this->_jc_categories = $db->loadObjectList();
					// now that we have the categories, we need the assets
					// here's the fun part, we need assets for both the categories AND the jcal stuff too!
					$db->setQuery((string) $db->getQuery(true)
						->select('*')
						->from($db->quoteName('#__assets'))
						->where($db->quoteName('name') . ' LIKE ' . $db->Quote($db->getEscaped($this->_jc_extension, true).'%', false))
					);
					$this->_jc_assets = $db->loadObjectList();
				}
			}
			catch (Exception $e) {
				// go ahead and tell the user we couldn't load the helper?
				$app->enqueuemessage(JText::sprintf('COM_JCALPRO_CANNOT_LOAD_HELPER', 'JCalPro'), 'error');
			}
		}
		*/
	}
	
	public function postflight($type, $parent) {
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		// enable the plugins
		$db->setQuery('UPDATE `#__extensions` SET `enabled`=1 WHERE (`element`="jcalpro" OR `element`="sh404sefextplugincom_jcalpro") AND `folder` IN ("system", "content", "sh404sefextplugins", "user") AND `type`="plugin"');
		$db->query();
		// try to fix frontend menus, if any are available
		$this->_fixFrontendMenus();
		if (in_array($type, array('install', 'discover_install', 'update'))) {
			// set the acls
			$this->_setACLs();
			// try to add default emails to database
			$this->_installEmailTemplates($parent);
		}
	}
	
	public function install($parent) {
		$this->parent = $parent->getParent();
	}

	public function update($parent) {
		$this->parent = $parent->getParent();
	}
	
	public function uninstall($parent) {
		$this->parent = $parent->getParent();
		/*
		$app = JFactory::getApplication();
		// check if we need to uninstall the database tables
		if ($this->_jc_uninstall) {
			$tables = array(
				'event_categories'
			,	'events'
			,	'forms'
			,	'fields'
			,	'form_fields'
			,	'registration'
			);
			$db = JFactory::getDbo();
			foreach ($tables as $table) {
				$db->setQuery('DROP TABLE IF EXISTS ' . $db->quoteName('#__jcalpro_' . $table));
				if ($db->query()) {
					$app->enqueuemessage(JText::sprintf('COM_JCALPRO_UNINSTALL_DATABASE_TABLE', $table), 'message');
				}
				else {
					$app->enqueuemessage(JText::sprintf('COM_JCALPRO_UNINSTALL_DATABASE_TABLE_ERROR', $table, $db->getErrorMsg()), 'error');
				}
			}
		}
		// looks like we're retaining data - so retain it
		// this is so dirty and makes me ashamed ;(
		else {
			// if we have categories, inject them into the categories table
			foreach (array('assets', 'categories') as $what) {
				if (!empty($this->{"_jc_$what"})) {
					foreach ($this->{"_jc_$what"} as $item) {
						$query = $db->getQuery(true)->insert("#__$what");
						// convert the category to an array, loop as key/values, and insert
						$array = JArrayHelper::fromObject($item);
						foreach ($array as $key => $value) $query->set($key, $value);
						die((string) $query);
						$db->setQuery((string) $query);
						$db->query();
					}
				}
			} 
		}
		*/
	}
	
	private function _installEmailTemplates(&$parent) {
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		$db->setQuery('SHOW TABLES LIKE "%jcalpro_emails"');
		try {
			$table = $db->loadResult();
		}
		catch (Exception $e) {
			// uh oh, not good!
			$app->enqueueMessage(JText::sprintf('COM_JCALPRO_INSTALLER_SQL_ERROR', $e->getMessage()), 'error');
			return;
		}
		
		if ($table) {
			// get the installed langauges for this site
			$langs = JLanguage::getKnownLanguages(JPATH_ROOT);
			$default = JLanguageHelper::detectLanguage();
			
			if (!is_array($langs)) {
				// uh oh, not good!
				$app->enqueueMessage(JText::_('COM_JCALPRO_INSTALLER_ERROR_NO_LANGS'));
				return;
			}
			$langs = array_keys($langs);
			
			if (is_null($default)) $default = '*';
			
			// the emails we're going to need
			$contexts = array(
				// events
				'event.admin.approve', 'event.user.added', 'event.user.approve',
				// registrations
				'registration.confirm', 'registration.confirmed', 'registration.notify'
			);
			// loop the languages and load the language file needed
			$base  = 'COM_JCALPRO_EMAIL_INSTALL';
			$check = "$base";
			foreach ($langs as $lang) {
				//if (JDEBUG) $app->enqueueMessage("Installing emails for language $lang (default $default)");
				$cxs = array_merge($contexts, array());
				// load this language
				JFactory::getLanguage()->load('com_jcalpro.emails', JPATH_ADMINISTRATOR, $lang, true);
				// check if this language loaded
				if (JText::_($base) == $check || '' == JText::_($base)) {
					//if (JDEBUG) $app->enqueueMessage("Language $lang not supported, found '$check' == JText::_('$base')");
					continue;
				}
				// if this is loaded, reset the check
				$check = JText::_($base);
				// load any existing emails for this language from the database
				$db->setQuery($db->getQuery(true)
					->select($db->quoteName('context'))
					->from('#__jcalpro_emails')
					->where($db->quoteName('language') . ' = ' . $db->quote($lang))
				);
				// try to get the emails
				try {
					$emails = $db->loadObjectList();
					// we have emails - see if we need to add this one
					if (!empty($emails)) {
						foreach ($emails as $email) {
							$key = array_search($email->context, $cxs);
							if (array_key_exists($key, $cxs) && $email->context == $cxs[$key]) {
								unset($cxs[$key]);
								$cxs = array_values($cxs);
							}
						}
					}
				}
				catch (Exception $e) {
					$app->enqueueMessage(JText::sprintf('COM_JCALPRO_INSTALLER_SQL_ERROR', $e->getMessage()), 'error');
					continue;
				}
				
				// let's see, we need to go ahead & try to insert each context, but only if
				// no other emails exist for this context - loop the contexts and check before
				// going further
				$values = array();
				foreach ($cxs as $i => $context) {
					$key      = strtoupper(str_replace('.', '_', $context));
					$body     = JText::_('COM_JCALPRO_EMAIL_DEFAULT_BODY_' . $key);
					$subject  = JText::_('COM_JCALPRO_EMAIL_DEFAULT_SUBJECT_' . $key);
					if ('' == $body || '' == $subject) continue;
					$values[] = $db->quote($context) . ','
					. $db->quote($body) . ','
					. $db->quote($subject) . ','
					. $db->quote($lang) . ','
					. ($default == $lang ? '1' : '0')
					;
				}
				if (!empty($values)) {
					$db->setQuery('INSERT IGNORE INTO #__jcalpro_emails (' . $db->quoteName('context') . ', ' . $db->quoteName('body') . ', ' . $db->quoteName('subject') . ', ' . $db->quoteName('language') . ', ' . $db->quoteName('default') . ') VALUES (' . implode('), (', $values) . ')');
					try {
						$db->query();
					}
					catch (Exception $e) {
						$app->enqueueMessage(JText::sprintf('COM_JCALPRO_INSTALLER_SQL_ERROR', $e->getMessage()), 'error');
						continue;
					}
				}
			}
		}
	}
	
	private function _setACLs() {
		$db = JFactory::getDbo();
		// get the rules for both the site AND the component, because we only want to reset
		// the moderate rules for the component IF they are empty
		$db->setQuery((string) $db->getQuery(true)
			->select('a.rules AS root_rules')
			->select('b.rules AS com_rules')
			->from('#__assets AS a')
			->leftJoin('#__assets AS b ON b.parent_id = a.id')
			->where('b.name = ' . $db->Quote('com_jcalpro'))
		);
		$rules = $db->loadObject();
		// parse the site rules
		$registry = new JRegistry();
		$registry->loadString($rules->root_rules);
		$root_rules = $registry->toArray();
		// parse the component rules
		$registry = new JRegistry();
		$registry->loadString($rules->com_rules);
		$com_rules = $registry->toArray();
		// check the component rules for moderation
		if (!array_key_exists("core.moderate", $com_rules) || empty($com_rules["core.moderate"])) {
			$com_rules["core.moderate"] = $root_rules["core.edit.state"];
		}
		// check the component rules for create private
		if (!array_key_exists("core.create.private", $com_rules) || empty($com_rules["core.create.private"])) {
			$com_rules["core.create.private"] = $root_rules["core.create"];
		}
		// check the component rules for field.create and field.edit
		foreach (array('create', 'edit') as $rule) {
			if (!array_key_exists("field.$rule", $com_rules)) {
				$com_rules["field.$rule"] = 1;
			}
		}
		// update the rules for the component
		$db->setQuery((string) $db->getQuery(true)
			->update('#__assets')
			->set($db->quoteName('rules') . ' = ' . $db->Quote(json_encode($com_rules)))
			->where($db->quoteName('name') . ' = ' . $db->Quote('com_jcalpro'))
		);
		$db->query();
		// fix the assets table for orphaned entries
		$this->_fixAssets();
	}
	
	private function _fixAssets() {
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('a.id')
			->from('#__assets AS a')
			->leftJoin('#__jcalpro_events AS e ON CONCAT(\'com_jcalpro.event.\',e.id) = a.name')
			->where('a.name LIKE "com_jcalpro.event.%"')
			->where('e.id IS NULL')
			->group('a.id')
		);
		
		try {
			$assets = $db->loadColumn();
		}
		catch (Exception $e) {
			return;
		}
		
		if (!empty($assets)) {
			$db->setQuery($db->getQuery(true)
				->delete('#__assets')
				->where('id IN (' . implode(',', $assets) . ')')
			);
			try {
				$db->query();
			}
			catch (Exception $e) {
				return;
			}
		}
		
		// try to fix old entries with incorrect parents
		
		// get the broken assets
		$db->setQuery($db->getQuery(true)
			->select('*')
			->from('#__assets')
			->where('name LIKE "com_jcalpro.event.%"')
			->where('level = 2')
		);
		try {
			$assets = $db->loadObjectList();
		}
		catch (Exception $e) {
			return;
		}
		
		// are there no broken assets? we're done here
		if (empty($assets)) return;
		
		// pull our event ids out
		$ids = array();
		foreach ($assets as $asset) {
			$ids[] = (int) str_replace('com_jcalpro.event.', '', $asset->name);
		}
		$ids = array_unique($ids);
		
		// now we need to know which category assets to assign to
		// get the category info from our xref table
		$db->setQuery($db->getQuery(true)
			->select('*')
			->from('#__jcalpro_event_categories')
			->where('canonical = 1')
			->where('event_id IN (' . implode(',', $ids) . ')')
		);
		
		try {
			$xrefs = $db->loadObjectList();
		}
		catch (Exception $e) {
			return;
		}
		
		// go through the assets and assign them
		foreach ($assets as $asset) {
			// find this asset's parent
			$id = (int) str_replace('com_jcalpro.event.', '', $asset->name);
			$catid = false;
			foreach ($xrefs as $xref) {
				if ($id == $xref->event_id) {
					$catid = (int) $xref->category_id;
					break;
				}
			}
			if (!$catid) continue;
			// load this category asset and update
			$db->setQuery($db->getQuery(true)
				->select('id')
				->from('#__assets')
				->where('name = ' . $db->quote('com_jcalpro.category.' . $catid))
			);
			
			try {
				$catasset = $db->loadResult();
			}
			catch (Exception $e) {
				continue;
			}
			
			// get a table for this asset and move it
			$table = JTable::getInstance('Asset');
			if ($table->load($asset->id)) {
				$table->moveByReference($catasset, 'last-child');
			}
			
		}
		
	}
	
	private function _fixFrontendMenus() {
		// get the current id of this component
		$db = JFactory::getDbo();
		$db->setQuery((string) $db->getQuery(true)
			->select('extension_id')
			->from('#__extensions')
			->where($db->quoteName('name') . '=' . $db->Quote('com_jcalpro'))
			->where($db->quoteName('type') . '=' . $db->Quote('component'))
		);
		$id = $db->loadResult();
		if ($id) {
			$db->setQuery((string) $db->getQuery(true)
				->update('#__menu')
				->set($db->quoteName('component_id') . '=' . intval($id))
				->where($db->quoteName('client_id') . '=0')
				->where($db->quoteName('link') . 'LIKE "index.php?option=com_jcalpro%"')
			);
			$db->query();
		}
	}
	
	/**
	 * Joomla! 1.6+ bugfix for "DB function returned no error"
	 */
	private function _bugfixDBFunctionReturnedNoError()
	{
		$db = JFactory::getDbo();
			
		// Fix broken #__assets records
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__assets')
			->where($db->quoteName('name').' = '.$db->Quote($this->_jc_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__assets')
				->where($db->quoteName('id').' = '.$db->Quote($id));
			$db->setQuery($query);
			$db->query();
		}

		// Fix broken #__extensions records
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where($db->quoteName('element').' = '.$db->Quote($this->_jc_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__extensions')
				->where($db->quoteName('extension_id').' = '.$db->Quote($id));
			$db->setQuery($query);
			$db->query();
		}

		// Fix broken #__menu records
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__menu')
			->where($db->quoteName('type').' = '.$db->Quote('component'))
			->where($db->quoteName('menutype').' = '.$db->Quote('main'))
			->where($db->quoteName('link').' LIKE '.$db->Quote('index.php?option='.$this->_jc_extension.'%'));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
				->where($db->quoteName('id').' = '.$db->Quote($id));
			$db->setQuery($query);
			$db->query();
		}
	}
	
	/**
	 * Joomla! 1.6+ bugfix for "Can not build admin menus"
	 */
	private function _bugfixCantBuildAdminMenus()
	{
		$db = JFactory::getDbo();
		
		// If there are multiple #__extensions record, keep one of them
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where($db->quoteName('element').' = '.$db->Quote($this->_jc_extension));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(count($ids) > 1) {
			asort($ids);
			$extension_id = array_shift($ids); // Keep the oldest id
			
			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__extensions')
					->where($db->quoteName('extension_id').' = '.$db->Quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}
		
		// If there are multiple assets records, delete all except the oldest one
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__assets')
			->where($db->quoteName('name').' = '.$db->Quote($this->_jc_extension));
		$db->setQuery($query);
		$ids = $db->loadObjectList();
		if(count($ids) > 1) {
			asort($ids);
			$asset_id = array_shift($ids); // Keep the oldest id
			
			foreach($ids as $id) {
				$query = $db->getQuery(true);
				$query->delete('#__assets')
					->where($db->quoteName('id').' = '.$db->Quote($id));
				$db->setQuery($query);
				$db->query();
			}
		}

		// Remove #__menu records for good measure!
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__menu')
			->where($db->quoteName('type').' = '.$db->Quote('component'))
			->where($db->quoteName('menutype').' = '.$db->Quote('main'))
			->where($db->quoteName('link').' LIKE '.$db->Quote('index.php?option='.$this->_jc_extension.'%'));
		$db->setQuery($query);
		$ids = $db->loadColumn();
		if(!empty($ids)) foreach($ids as $id) {
			$query = $db->getQuery(true);
			$query->delete('#__menu')
				->where($db->quoteName('id').' = '.$db->Quote($id));
			$db->setQuery($query);
			$db->query();
		}
	}
	
}