<?php
/**
 * @version		$Id: joomprosubs.php 283 2011-11-10 23:31:14Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_joomprosubs
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Include dependencies
jimport('joomla.application.component.controller');

$controller	= JController::getInstance('Joomprosubs');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();