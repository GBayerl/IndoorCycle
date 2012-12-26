<?php
/**
 *
 * @package Joomla.Administrator
 * @subpackage com_courses
 * @copyright (c) 2012, Guenther Bayerl. All rights reserved
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class CoursesControllerCourses extends JControllerAdmin
{
    /**
     * Proxy for getModel
     */
    
    public function getModel($name = 'Subscription', $prefix = 'CoursesModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}