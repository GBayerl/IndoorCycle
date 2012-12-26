<?php
/**
 *
 * @package Joomla.Administrator
 * @subpackage com_courses
 * @copyright (c) 2012, Guenther Bayerl. All rights reserved
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Courses helper.
 * 
 */

class CoursesHelper
{
    /**
     * Configure the Linkbar.
     * 
     * @param string The name of the active view.
     * 
     */
    
    public static function addSubmenu($vName = 'courses')
    {
        JSubMenuHelper::addEntry(
                JText::_('COM_COURSES_SUBMENU_COURSES'),
                'index.php?option=com_courses&view=courses',
                $vName == 'courses'
                );
        JSubMenuHelper::addEntry(
                JText::_('COM_COURSES_SUBMENU_CATEGORIES'),
                'index.php?option=com_categories&extension=com_courses',
                $vName == 'categories'
                );
        if ($vName=='categories')
        {
            JToolBarHelper::title(JText::sprintf('COM_CATEGORIES_TITLE', JText::_('com_courses')), 'courses-categories');
        }
    }
    
    /**
     * Gets a list of the actions that can be performed.
     * 
     * @param int The category id
     * 
     */
    
    public static function getActions($categoryId = 0)
    {
        
        echo $categoryId & "<br />";
        $user = JFactory::getUser();
        $result = new Object;
        
        if (empty($categoryId))
        {
            $assetName = 'com_courses';
        } else
        {
            $assetName = 'com_courses.category.'.(int) $categoryId;
        }
        
        $actions = array('core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete');
        echo $categoryId;
        foreach ($actions as $action)
        {
            $result->set($action, $user->authorise($action, $assetName));
        }
        
        var_dump($result);
        return $result;
    }
}