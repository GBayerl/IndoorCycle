<?php
/**
 * @version		$Id: jclcaptcha.php 772 2012-04-17 19:21:09Z jeffchannell $
 * @package		JCalPro
 * @subpackage	plg_content_jclcaptcha

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
JLoader::register('JCalPro', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/jcalpro.php');
JCalPro::language('plg_content_jclcaptcha.sys', JPATH_ADMINISTRATOR);

class JFormFieldJclCaptcha extends JFormField
{
	public $type = 'Jclcaptcha';

	protected function getInput() {
		// we have to ensure we have a public key
		// we can't determine here if it's valid, except if it's empty
		$plugin = JPluginHelper::getPlugin('content', 'jclcaptcha');
		$registry = new JRegistry();
		$registry->loadString($plugin->params);
		$key = $registry->get('recaptcha_public_key');
		if (empty($key)) return JText::_('PLG_CONTENT_JCLCAPTCHA_NO_PUBLIC_KEY');
		// the document
		$doc = JFactory::getDocument();
		// the name of our element
		$elname = $this->id . '_recaptcha_div';
		// build the script declaration
		$script = array();
		$script[] = "window.addEvent('load', function() {";
		$script[] = "	" . sprintf("Recaptcha.create('%s', '%s', {});", $key, $elname);
		$script[] = "});";
		// add our scripts
		$doc->addScript('http://api.recaptcha.net/js/recaptcha_ajax.js');
		$doc->addScriptDeclaration(implode("\n", $script));
		// add our element
		return '<div id="' . $elname . '">' . JText::_('PLG_CONTENT_JCLCAPTCHA_LOADING_CAPTCHA') . '</div>';
	}
}
