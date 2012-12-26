<?php
/**
 * @version    $Id$
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
jimport('joomla.plugin.plugin');

/**
 * This is our custom registration plugin class.  It verifies that the user checked the boxes
 * indicating that he/she agrees to the terms of service and is old enough to use the site.
 *
 * @package     Joomla.Plugins
 * @subpackage  User.MyRegistration
 * @since       1.0
 */
class plgUserMyRegistration2 extends JPlugin
{	
	
	/**
	 * Method to handle the "onContentPrepareForm" event and alter the user registration form.  We
	 * are going to check and make sure that the form being prepared is the user registration form
	 * from the com_users component first.  If that is the form we are preparing, then we will
	 * load our custom xml file into the form object which adds our custom fields.
	 * 
	 * @param   JForm  $form  The form to be altered.
	 * @param   array  $data  The associated data for the form.
	 *
	 * @return  bool
	 * 
	 * @since   1.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		// If we aren't in the registration form ignore the form.
		if ($form->getName() != 'com_users.registration') {
			return;
		}

		// Load the plugin language file
		$this->loadLanguage();
		
		// Load our custom registration xml into the user registration form.
		$form->loadFile(dirname(__FILE__).'/forms/form.xml');
//		if (!$this->params->def('show_age_checkbox', '1')) {
//			$form->removeField('old_enough');
//		}
		$form->setFieldAttribute('old_enough', 'required', 'false');
		
	}
	
}
