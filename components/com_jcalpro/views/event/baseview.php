<?php
/**
 * @version		$Id: baseview.php 837 2012-11-28 17:31:43Z jeffchannell $
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

jimport('joomla.application.component.view');

JLoader::register('JCalProView', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/views/baseview.php');

/**
 * JCalPro event view.
 *
 * @package		JCalPro
 * @subpackage	com_jcalpro
 */
class JCalProViewEvent extends JCalProView
{
	function display($tpl = null, $echo = true) {
		
		$app = JFactory::getApplication();
		// we need to set the format in the model's state in case of ical
		$format = $app->input->get('format', 'html', 'cmd');
		$this->extmode = 'event';
		
		$form = $this->get('Form');
		$item = $this->get('Item');
		$categories = $this->get('Categories');
		$user = JFactory::getUser();
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// check this event
		if ($item && $item->id && (1 != $item->published || 1 != $item->approved)) {
			// check permissions here
			if (!JCalPro::canModerateEvents(@$item->canonical->id)) {
				if ($user->guest) {
					$app->enqueueMessage(JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'), 'error');
					$app->redirect(JRoute::_('index.php?option=com_users&view=login'));
				}
				else if ($user->id != $item->created_by) {
					JError::raiseError(404, JText::_('COM_JCALPRO_ERROR_PAGE_NOT_FOUND'));
				}
				jexit();
			}
		}
		
		// Assign the Data
		$this->form       = $form;
		$this->item       = $item;
		$this->user       = $user;
		$this->categories = $categories;
		$this->category   = false;
		$this->title      = $item->title;
		
		JCalPro::debugger('Item', $this->item);
		JCalPro::debugger('Form', $this->form);
		JCalPro::debugger('Categories', $this->categories);
		
		$catid = $app->getUserStateFromRequest('com_jcalpro.events.jcal.catid', 'catid', 0);
		foreach ($categories as $cat) {
			if ($cat->id == $catid) {
				$this->category = $cat;
				break;
			}
		}
		
		// switch the different view modes depending on format
		switch ($format) {
			// ical format, register the ical helper, process the ical and exit
			case 'ical':
				JLoader::register('JCalProHelperIcal', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/ical.php');
				echo JCalProHelperIcal::toIcal(array($this->item));
				jexit();
				break;
			// all other formats we just display normally
			default:
				if ('edit' != $app->input->get('layout', '', 'cmd')) {
					// fix the theme for this item
					$this->template = $this->item->categories->canonical->params->get('jcalpro_theme');
					// add the registration button if this event allows it
					if ($this->item->allow_registration && $this->item->registration
					&& isset($this->item->registration_data)
					&& is_object($this->item->registration_data)
					&& $this->item->registration_data->can_register
					) {
						// TODO move this to a helper
						$this->buttons = array(
							'register' => array(
								'name' => 'register'
							,	'title' => JText::_('COM_JCALPRO_MAINMENU_REGISTER')
							,	'href' => JCalProHelperUrl::task('registration.add', true, array('event_id' => $this->item->id))
							,	'class' => array("jcl_toolbar_button", "jcl_toolbar_button_register")
							,	'attr' => array()
							)
						);
					}
					// go ahead and build the form field layouts
					$displaytypes = array('hidden', 'header', 'top', 'bottom', 'side');
					$fields = new stdClass;
					foreach ($displaytypes as $dt) $fields->{$dt} = array();
					// now grab the fields for this event
					$formid = (int) @$this->item->categories->canonical->params->get('jcalpro_eventform');
					JLoader::register('JCalProHelperForm', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/form.php');
					$formfields = JCalProHelperForm::getFields($formid);
					// load any found fields and add them to the appropriate places
					if (!empty($formfields)) {
						foreach ($formfields as $field) {
							if (array_key_exists($field->name, $this->item->params)) {
								$fields->{$displaytypes[$field->event_display]}[] = $field;
							}
						}
					}
					$this->item->formid = $formid;
					$this->item->formfields = $formfields;
					
					// assign the form field layouts to the item
					$this->item->custom_fields = $fields;
				}
				
				// display
				parent::display($tpl);
		}
	}
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument() {
		parent::_prepareDocument();
		if ('edit' == $this->_layout) {
			JHtml::_('behavior.framework', true);
			$this->document->addScriptDeclaration('window.jclDateTimeCheckUrl = \'' . JCalProHelperFilter::escape_js(JCalProHelperUrl::task('event.checkdate')) . '\';');
			$this->document->addScript(JCalProHelperUrl::media() . '/js/event.js');
			$this->document->addStyleSheet(JCalProHelperUrl::media() . '/css/event.css');
		}
	}
}
