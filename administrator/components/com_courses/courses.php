<?php
/**
 *
 * @package Joomla.Administrator
 * @subpackage com_courses
 * @copyright (c) 2012, Guenther Bayerl. All rights reserved
 */

// no direct access
defined('_JEXEC') or die;

// Access check
if (!JFactory::getUser()->authorise('core.manage', 'com_courses'))
{
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependencies
jimport('joomla.application.component.controller');
$controller = JController::getInstance('Courses');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();


