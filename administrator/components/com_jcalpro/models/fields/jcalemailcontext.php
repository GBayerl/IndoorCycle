<?php
/**
 * @version		$Id: jcalemailcontext.php 807 2012-10-02 18:53:43Z jeffchannell $
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

jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldJCalEmailContext extends JFormField
{
	public $type = 'Jcalemailcontext';

	protected function getInput() {
		// get class for this element
		$class = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		
		$list = array();
		// some defaults
		$list[] = JHtml::_('select.option', 'registration.confirm', JText::_('COM_JCALPRO_EMAIL_CONTEXT_REGISTRATION_CONFIRM'));
		$list[] = JHtml::_('select.option', 'registration.confirmed', JText::_('COM_JCALPRO_EMAIL_CONTEXT_REGISTRATION_CONFIRMED'));
		$list[] = JHtml::_('select.option', 'registration.notify', JText::_('COM_JCALPRO_EMAIL_CONTEXT_REGISTRATION_NOTIFY'));
		
		JDispatcher::getInstance()->trigger('onJCalEmailContextList', array(&$list));
		
    return JHtml::_('select.genericlist', $list, $this->name, $class . ' size="1"', 'value', 'text', $this->value);
	}
}
