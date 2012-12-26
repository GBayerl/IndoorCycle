<?php
defined ('_JEXEC') or die;
jimport ('joomla.application.component.controller');

$controller = JController::getInstance('courses');

$input = JFactory::getApplication()->input;
$controller->execute($input->get('task'));