<?php
/**
 * @version		$Id: joomprosubs.php 272 2011-08-11 00:32:05Z dextercowley $
 * @package		Joomla.Administrator
 * @subpackage	com_joomprosubs
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_joomprosubs')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JController::getInstance('JoomproSubs');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();