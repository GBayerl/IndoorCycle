<?php
/**
 *
 * @package Joomla.Administrator
 * @subpackage com_courses
 * @copyright (c) 2012, Guenther Bayerl. All rights reserved
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * 
 * Courses controller class.
 * 
 */

class CoursesControllerCourse extends JControllerForm
{
    /**
     * The URL view list variable.
     * 
     * @var string
     */
    
    // protected $view_list = 'courses';
    
    /**
     * 
     * Method override to check if you can add a new record.
     * 
     * @param array $data An array of input data.
     * @return boolean Description
     * 
     */
    protected function allowAdd($data = array())
    {
        // Initialise variables.
        $user = JFactory::getUser();
        $categoryId = JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
        $allow = null;
        
        if ($categoryId)
        {
            // If the category has been passed in the URL check it.
            $allow = $user->authorise('core.create', $this->option.'.category.'.$categoryId);
        }
        
        if ($allow === null)
        {
            // in the absense of better information, revert to the component permissions.
            return parent::alowAdd($data);
        } else {
            return $allow;
        }
    }
    
     /**
     * 
     * Method override to check if you can edit a record.
     * 
     * @param array $data An array of input data.
     * @return boolean Description
     * 
     */
    
    protected function allowEdit($data = array(), $key = 'id')
    {
        // Initialise variables.
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $categoryId = 0;
        
        if ($recordId)
        {
            $categoryId = (int) $this->getModel()->getItem($recordId)->catid;
        }
        
        if ($categoryId)
        {
            // The category has been set. Check the category permissions.
            return JFactory::getUser()->authorise('core.edit', $this->option.'category.'.$categoryId);
        } else {
            // Since there is no asset tracking, revert to the component permissions.
            return parent::alowEdit($data, $key);
        }
    }
}