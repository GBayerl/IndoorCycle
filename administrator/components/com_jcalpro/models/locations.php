<?php
/**
 * @version		$Id: locations.php 790 2012-05-28 19:36:06Z jeffchannell $
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

JLoader::register('JCalProListLocationsModel', JPATH_ADMINISTRATOR.'/components/com_jcalpro/libraries/models/basemodellocations.php');

/**
 * This models supports retrieving lists of locations.
 *
 * @package		JCalPro
 * @subpackage	com_jcalpro
 */
class JCalProModelLocations extends JCalProListLocationsModel
{
	/**
	 * Creates a new JForm to display the rules for locations
	 * 
	 * @return JForm
	 */
	public function getPermissionsForm() {
		// build our form
		jimport('joomla.form.form');
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_jcalpro/models/forms');
		$form = JForm::getInstance('com_jcalpro.locations', 'locations');
		// check the form
		if (!($form instanceof JForm)) {
			JError::raiseError(500, JText::_('JERROR_NOT_A_FORM'));
			jexit();
		}
		// get the asset data & bind it to the form
		$db = JFactory::getDbo();
		$db->setQuery($db->getQuery(true)
			->select('id, rules')
			->from('#__assets')
			->where('name = ' . $db->Quote('com_jcalpro.locations'))
		);
		$data = $db->loadObject();
		if (!empty($data)) {
			$form->bind(array('asset_id' => $data->id, 'rules' => $data->rules));
		}
		// all done - return form
		return $form;
	}
}
