<?php
/**
 * @version		$Id: jcalpro.php 814 2012-10-10 23:44:59Z jeffchannell $
 * @package		JCalPro
 * @subpackage	plg_content_jcalpro

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

jimport('joomla.plugin.plugin');
JLoader::register('JCalPro', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/jcalpro.php');
JLoader::register('JCalProHelperLog', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/log.php');
JLoader::register('JCalProHelperMail', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/mail.php');
JLoader::register('JCalProHelperUrl', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/url.php');

class plgContentJCalPro extends JPlugin
{
	public static $com = 'com_jcalpro';
	
	private static $_event_approved = false;
	
	/**
	 * Constructor
	 * 
	 * @param unknown_type $subject
	 * @param unknown_type $config
	 */
	public function __construct(&$subject, $config = null) {
		JCalPro::language(self::$com . '.event', JPATH_ADMINISTRATOR);
		parent::__construct($subject, $config);
	}
	
	/**
	 * onContentPrepareForm
	 * 
	 * @param JForm $form
	 */
	public function onContentPrepareForm($form) {
		JCalProHelperLog::debug(__METHOD__);
		if (!($form instanceof JForm)) {
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}
		if ('com_categories.categorycom_jcalpro' != $form->getName()) return true;
		JCalPro::language(self::$com, JPATH_ADMINISTRATOR);
		JForm::addFieldPath(JPATH_ADMINISTRATOR . "/components/".self::$com."/models/fields");
		JForm::addFormPath(JPATH_ADMINISTRATOR . "/components/".self::$com."/models/forms");
		return $form->loadFile('jcalprocategory', false);
	}
	
	/**
	 * Don't allow categories to be deleted if they contain events or subcategories with events
	 * 
	 * @param       string  The context for the content passed to the plugin.
	 * @param       object  The data relating to the content that was deleted.
	 * @return      boolean
	 */
	public function onContentBeforeDelete($context, $data) {
		JCalProHelperLog::debug(__METHOD__ . '(' . $context . ')');
		// Skip plugin if we are deleting something other than categories
		if ('com_categories.category' != $context) return true;
		// ensure we're only handling our own
		if (JFactory::getApplication()->input->get('extension', '', 'string') != self::$com) return true;
		// Default to true
		$result = true;
		// See if this category has any events
		$count = $this->_countEventsInCategory($data->get('id'));
		// Return false if db error
		if (false === $count) {
			$result = false;
		}
		else {
			// Show error if items are found in the category
			if (0 < $count) {
				$msg = JText::sprintf('COM_CATEGORIES_DELETE_NOT_ALLOWED', $data->get('title')) .
				JText::plural('COM_CATEGORIES_N_ITEMS_ASSIGNED', $count);
				JError::raiseWarning(403, $msg);
				$result = false;
			}
			// Check for items in any child categories (if it is a leaf, there are no child categories)
			if (!$data->isLeaf()) {
				$count = $this->_countEventsInChildren($data);
				if (false === $count) {
					$result = false;
				}
				else if (0 < $count) {
					$msg = JText::sprintf('COM_CATEGORIES_DELETE_NOT_ALLOWED', $data->get('title')) .
					JText::plural('COM_CATEGORIES_HAS_SUBCATEGORY_ITEMS', $count);
					JError::raiseWarning(403, $msg);
					$result = false;
				}
			}
		}
		// check our result - if it's true, purge this category from events where it's not canonical
		if ($result) {
			$db = JFactory::getDbo();
			$db->setQuery((string) $db->getQuery(true)
				->delete('#__jcalpro_event_categories')
				->where('category_id = ' . $data->get('id'))
				->where('canonical = 0')
			);
			$db->query();
			if ($error = $db->getErrorMsg()) {
				JError::raiseWarning(500, $error);
				return false;
			}
			return true;
		}
		// if we make it this far, we failed :P
		return false;
	}
	
	/**
	 * Get count of items in a category
	 * 
	 * @param       int     id of the category to check
	 * @return      mixed   count of items found or false if db error
	 */
	private function _countEventsInCategory($catid) {
		$db = JFactory::getDbo();
		$db->setQuery((string) $db->getQuery(true)
			->select('COUNT(event_id)')
			->from('#__jcalpro_event_categories')
			->where('category_id = ' . (int) $catid)
			->where('canonical = 1')
		);
		$count = $db->loadResult();
		// Check for DB error.
		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
			return false;
		}
		return $count;
	}
	
	/**
	 * Get count of items in a category's child categories
	 * 
	 * @param       object
	 * @return      mixed   count of items found or false if db error
	 */
	private function _countEventsInChildren($data) {
		$db = JFactory::getDbo();
		// Create subquery for list of child categories
		$childCategoryTree = $data->getTree();
		// First element in tree is the current category, so we can skip that one
		unset($childCategoryTree[0]);
		$childCategoryIds = array();
		foreach ($childCategoryTree as $node) {
			$childCategoryIds[] = (int) $node->id;
		}
		// Make sure we only do the query if we have some categories to look in
		if (count($childCategoryIds)) {
			// Count the items in this category
			$db->setQuery((string) $db->getQuery(true)
				->select('COUNT(event_id)')
				->from('#__jcalpro_event_categories')
				->where('category_id IN (' . implode(',', $childCategoryIds) . ')')
				->where('canonical = 1')
			);
			$count = $db->loadResult();
			
			// Check for DB error.
			if ($error = $db->getErrorMsg()) {
				JError::raiseWarning(500, $error);
				return false;
			}
			return $count;
		}
		// If we didn't have any categories to check, return 0
		return 0;
	}

	public function onJCalChangeApproval($context, $pks, $value) {
		JCalProHelperLog::debug(__METHOD__ . '("' . $context . '", "' . implode(',', $pks) . '", "' . (int) $value . '")');
		$jcontext = $this->_getContext($context);
		switch ($jcontext) {
			case 'event':
			case 'events':
				if (!$value) break;
				if (!empty($pks)) foreach ($pks as $pk) {
					$table = JTable::getInstance('Event', 'JCalProTable');
					$table->load($pk);
					if (!$table->id/* || $table->approved == $value*/) continue;
					self::$_event_approved = true;
					$this->onJCalAfterSave(self::$com . '.event', $table, false);
					self::$_event_approved = false;
				}
				break;
		}
	}
	
	/**
	 * handle the before save event
	 * 
	 * @param $context
	 * @param $table
	 * @param $isNew
	 */
	public function onJCalBeforeSave($context, &$table) {
		JCalProHelperLog::debug(__METHOD__ . '(' . $context . ')');
		$jcontext = $this->_getContext($context);
		switch ($jcontext) {
			case 'event':
				$approved = false;
				// if the event is not new, check to see if it's already been approved
				if (!$isNew && !((bool) $table->approved)) {
					// not approved, probably being approved...
					$approved = true;
				}
				self::$_event_approved = $approved;
				// THIS DOESN'T WORK!!!
				/*
				// is the event approved ?
				$check = (bool) $table->approved;
				// is it an existing event? if so, was it already approved?
				if ($table->id) {
					$checkTable = JTable::getInstance('Event', 'JCalProTable');
					$checkTable->load($table->id);
					$check = $check && ($checkTable->approved != $table->approved);
				}
				// set approval check flag
				self::$_event_approved = $check;// && $table->id;
				*/
				break;
			case 'field':
				$data = JFactory::getApplication()->input->post->get('jform', array(), 'array');
				if (array_key_exists('rules', $data)) {
					$table->setRules($data['rules']);
				}
				break;
		}
	}                
	
	/**
	 * handle the after save event
	 * 
	 * @param $context
	 * @param $table
	 * @param $isNew
	 */
	public function onJCalAfterSave($context, &$table, $isNew) {
		JCalProHelperLog::debug(__METHOD__ . '(' . $context . ')');
		$jcontext = $this->_getContext($context);
		// we use different private methods here depending on context
		switch ($jcontext) {
			
			case 'registration' :
			case 'event'        :
				// for now this ONLY handles new items, either registrations or events
				// TODO: handle existing items if needed
				//if (!$isNew) return;
				$method = '_send' . ucwords($jcontext) . 'Emails';
				JCalProHelperLog::debug("Running " . __CLASS__ . "::$method()...");
				return self::$method($table, $isNew);
				
			case 'field'        :
				// we have to manually update the rules for a field
				// because the core JAccessRule forces the rules into integers
				// this means that "Inherit" gets changed to "Denied" and is wrong
				$data  = JFactory::getApplication()->input->post->get('jform', array(), 'array');
				$rules = array();
				if (!empty($data) && array_key_exists('rules', $data)) {
					foreach ($data['rules'] as $action => $identities) {
						if (!empty($identities)) {
							foreach ($identities as $group => $permission) {
								if ('' == $permission) continue;
								$rules[$action][$group] = (int) ((bool) $permission);
							}
						}
					}
				}
				// update the asset
				$db = JFactory::getDbo();
				$db->setQuery($db->getQuery(true)
					->update('#__assets')
					->set('rules = ' . $db->Quote(json_encode($rules)))
					->where('id = ' . (int) $table->asset_id)
				);
				$db->query();
				break;
		}
	}
	
	public function onJCalEmailContextList($list) {
		JCalProHelperLog::debug(__METHOD__);
		$list = array_merge($list, array(
			JHtml::_('select.option', 'event.admin.approve', JText::_('COM_JCALPRO_EMAIL_CONTEXT_EVENT_ADMIN_APPROVE'))
		,	JHtml::_('select.option', 'event.user.added',    JText::_('COM_JCALPRO_EMAIL_CONTEXT_EVENT_USER_ADDED'))
		,	JHtml::_('select.option', 'event.user.approve',  JText::_('COM_JCALPRO_EMAIL_CONTEXT_EVENT_USER_APPROVE'))
		));
	}
	
	private function _getContext($context) {
		return str_replace(self::$com . '.', '', $context);
	}
	
	/**
	 * sends out notification emails to moderators when a new event is added
	 * also send out a generic confirmation to the user that submitted the event, if not a guest
	 * 
	 * @param $table
	 */
	private function _sendEventEmails(&$table, &$isNew) {
		// get the user from the event & send them an email (if allowed by their settings)
		$user = JFactory::getUser($table->created_by);
		// load the event model and get the fully parsed event
		$model = JCalPro::getModelInstance('Event', 'JCalProModel');
		$event = $model->getItem($table->id);
		// if this event is approved already, then it's either been created by a moderator
		// or it's a private event. regardless of which one, we don't need to notify the mods
		if ($table->approved && (self::$_event_approved || $isNew)) {
			// ensure we have a user :)
			if ($user->id) {
				// UPDATE: use mail helper to send user-defined emails
				JCalProHelperMail::send('event.user.added', $event, $user);
				// start building the submission courtesy email
				return;
			}
			else {
				JCalProHelperLog::debug("Approved event with no user, not sending event.user.added ...");
			}
		}
		// we only want to send out the moderator email if the event has approved = 0 and it's new
		else if ($isNew && 0 === (int) $table->approved) {
			// we need to get the email addresses for the moderators
			$mods = JCalProHelperMail::getModerators($event->categories->canonical->id);
			if (!empty($mods)) {
				// UPDATE: use mail helper to send user-defined emails
				foreach ($mods as $mod) JCalProHelperMail::send('event.admin.approve', $event, $mod);
			}
			// now send the user an email telling them that their event is awaiting approval
			if ($user->id) {
				// UPDATE: use mail helper to send user-defined emails
				JCalProHelperMail::send('event.user.approve', $event, $user);
				return;
			}
		}
		else {
			JCalProHelperLog::debug("No emails to send, details:\n" . print_r(array('table.approved' => (int) $table->approved, 'table.isnew' => ($isNew ? 'true' : 'false'), 'table.isapproved' => (self::$_event_approved ? 'true' : 'false')), 1));
		}
	}
	
	/**
	 * sends out a registration notification email
	 * 
	 * @param unknown_type $table
	 */
	private function _sendRegistrationEmails(&$table, &$isNew) {
		// for now, fix this
		if (!$isNew) return;
		$model = JCalPro::getModelInstance('Event', 'JCalProModel');
		$event = $model->getItem($table->event_id);
		if (!$event) return;
		
		// ensure we have an unpublisged registration
		if (!property_exists($event, 'registration_data') || $table->published) return;
		
		// set some extra data
		$table->confirmhref = JCalProHelperUrl::toFull(JCalProHelperUrl::task('registration.confirm', true, array('token' => $table->confirmation)));
		$table->details = JCalProHelperMail::buildEventData($event);
		
		// now set registration in the event & pass to the helper
		$event->registration_data->current_entry = $table;
		
		$user = JFactory::getUser($table->user_id);
		if (!$user->id) {
			$user = new stdClass;
			$user->email    = $table->user_email;
			$user->name     = $table->user_name;
			$user->username = $table->user_name;
		}
		
		// UPDATE: use mail helper to send user-defined emails
		JCalProHelperMail::send('registration.confirm', $event, $user);
		
		// unset the things we added
		unset($event->registration_data->current_entry);
		unset($table->confirmhref);
		unset($table->details);
		
		/*
		$details   = JCalProHelperMail::getSiteDetails();
		$url       = JCalProHelperUrl::toFull(JCalProHelperUrl::task('registration.confirm', true, array('token' => $table->confirmation)));
		$name      = $table->user_name;
		$email     = $table->user_email;
		$eventdata = JCalProHelperMail::buildEventData($event);
		$subject   = JText::sprintf('COM_JCALPRO_CONFIRMATION_SUBJECT', $details['sitename']);
		$body      = JText::sprintf('COM_JCALPRO_CONFIRMATION_BODY', $event->title, $details['sitename'], JUri::root(), $url, $eventdata);
		// build the mail
		JCalProHelperMail::mail($email, $subject, $body);
		*/
	}
}

JDispatcher::getInstance()->register('onJCalChangeApproval', 'plgContentJCalPro');