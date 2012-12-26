<?php
/**
 * @version		$Id: view.html.php 804 2012-09-17 15:14:09Z jeffchannell $
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

JLoader::register('JCalProListView', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/views/baseviewlist.php');

class JCalProViewRegistrations extends JCalProListView
{
	function display($tpl = null, $echo = true) {
		// build the event id filter using the JForm element
		//$this->addFilter(JText::_('COM_JCALPRO_EVENT'), 'filter_event', $this->get('EventFilter'), $this->get('State')->get('filter.event'));
		// display the list
		parent::display($tpl, $echo);
	}

	public function addToolBar() {
		// add our export buttons
		JToolBarHelper::custom('registrations.export','export.png','export.png','JTOOLBAR_EXPORT', false);
		JToolBarHelper::divider();
		// this is copy/pasted from the base view list, as we cannot use the base here
		// if we do, we end up with "(Un)Published" instead of "(Un)Confirmed"
		$single = preg_replace('/s$/', '', $this->_name);
		// set the toolbar title
		JToolBarHelper::title(JText::_(strtoupper(self::$option.'_'.$this->_name.'_MANAGER')), 'jcalpro-'.strtolower($this->_name));
		if (JFactory::getUser()->authorise('core.create')) {
			JToolBarHelper::addNew($single . '.add', 'JTOOLBAR_NEW');
		}
		if (JFactory::getUser()->authorise('core.edit') || JFactory::getUser()->authorise('core.edit.own')) {
			JToolBarHelper::editList($single . '.edit', 'JTOOLBAR_EDIT');
			JToolBarHelper::divider();
		}
		if (JFactory::getUser()->authorise('core.edit.state')) {
			JToolBarHelper::publish($this->_name . '.publish', 'COM_JCALPRO_REGISTRATIONS_CONFIRM', true);
			JToolBarHelper::unpublish($this->_name . '.unpublish', 'COM_JCALPRO_REGISTRATIONS_UNCONFIRM', true);
			JToolBarHelper::checkin($this->_name . '.checkin');
			JToolBarHelper::divider();
		}
		if ($this->state->get('filter.published') == -2 && JFactory::getUser()->authorise('core.delete', self::$option)) {
			JToolBarHelper::deleteList('', $this->_name . '.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		else if (JFactory::getUser()->authorise('core.edit.state')) {
			JToolBarHelper::trash($this->_name . '.trash');
			JToolBarHelper::divider();
		}
		// from the base view
		if (JFactory::getUser()->authorise('core.manage', self::$option)) {
			JToolBarHelper::preferences(self::$option);
		}
		
		JToolBarHelper::divider();
		// help!!!
		JToolBarHelper::help('COM_JCALPRO_HELP', false, JCalPro::config('jcalpro_help_url'));
	}
}