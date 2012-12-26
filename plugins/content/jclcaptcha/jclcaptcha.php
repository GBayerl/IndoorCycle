<?php
/**
 * @version		$Id: jclcaptcha.php 823 2012-10-23 06:08:24Z jeffchannell $
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

jimport('joomla.plugin.plugin');
JLoader::register('JCalPro', JPATH_ADMINISTRATOR.'/components/com_jcalpro/helpers/jcalpro.php');

class plgContentJclCaptcha extends JPlugin
{
	/**
	 * private variable to store the path for this plugin file
	 * 
	 * @var string
	 */
	private $path;
	
	private $_contexts = array('com_jcalpro.event', 'com_jcalpro.registration');
	
	/**
	 * Constructor
	 * 
	 * @param unknown_type $subject
	 * @param unknown_type $config
	 */
	public function __construct(&$subject, $config) {
		JCalPro::language('plg_content_jclcaptcha.sys', JPATH_ADMINISTRATOR);
		$this->path = dirname(__FILE__);
		parent::__construct($subject, $config);
	}
	
	/**
	 * onContentPrepareForm
	 * 
	 * @param JForm $form
	 */
	public function onContentPrepareForm($form) {
		if (JFactory::getApplication()->isAdmin() || !in_array($form->getName(), $this->_contexts)) return true;
		$form->addFormPath($this->path . "/include/forms");
		//$form->addFieldPath($this->path . "/include/fields");
		require_once $this->path . '/include/libraries/recaptcha/recaptcha.php';
		return $form->loadFile('jclcaptcha', false);
	}
	
	/**
	 * checks the captcha to see if it's valid
	 * 
	 * @param $context
	 * @param $table
	 * @param $isNew
	 */
	public function onJCalBeforeSave($context, &$table, $isNew) {
		$app = JFactory::getApplication();
		if ($app->isAdmin() || !in_array($context, $this->_contexts)) return true;
		$privatekey = $this->params->get('recaptcha_private_key');
		if (empty($privatekey)) return true;
		require_once $this->path . '/include/libraries/recaptcha/recaptcha.php';
		$challenge  = $app->input->get("recaptcha_challenge_field", null, null);
		$response   = $app->input->get("recaptcha_response_field", null, null);
		$check      = jcl_recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $challenge, $response);
		if (!$check->is_valid) {
			$message = JText::_('PLG_CONTENT_JCLCAPTCHA_INVALID');
			$error   = trim((string) $check->error);
			switch ($error) {
				case 'incorrect-captcha-sol':
					break;
				default:
					$message .= empty($error) ? '' : ' (' . $error . ')';
					break;
			}
			if (defined('JDEBUG') && JDEBUG) {
				$message .= '<br>Challenge: ' . htmlspecialchars($challenge);
				$message .= '<br>Response: ' . htmlspecialchars($response);
				$message .= '<br>Check: <pre>' . htmlspecialchars(print_r($check, 1)) . '</pre>';
			}
			$app->enqueueMessage($message, 'error');
			return false;
		}
		return true;
	}
	
}
