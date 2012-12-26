<?php
/**
 *
 * @package Joomla.Administrator
 * @subpackage com_courses
 * @copyright (c) 2012, Guenther Bayerl. All rights reserved
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of courses
 * 
 */

class CoursesViewCourses extends JView
{
    protected $items;
    protected $pagination;
    protected $state;
    
    /**
     * Display the view
     * 
     */
    public function display($tpl=nul)
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        
        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();

        parent::display($tpl);
    }
    
    /**
     * 
     * Add the page title and toolbar to the view
     * @since 2.5
     * 
     */
    
    protected function addToolbar()
    {
        JLoader::register('CoursesHelper', JPATH_COMPONENT.'/helpers/courses.php');
        $state  = $this->get('State');

        // Debug Information

        echo "<br />State: " . var_dump($state);
        echo "<br />canDo: " . $state->get('filter.category_id');
        
        $canDo  = CoursesHelper::getActions($state->get('filter.category_id'));
        $user   = JFactory::getUser();
        JToolBarHelper::title(JText::_('COM_COURSES_MANAGER_COURSES'), 'newsfeeds.png');
        
        if (count($user->getAuthorisedCategories('com_courses', 'core.create')) > 0)
        {
            JToolBarHelper::addNew('course.add', 'JTOOLBAR_NEW');
        }
        
        if ($canDo->get('core.edit'))
        {
            JToolBarHelper::editList('course.edit', 'JTOOLBAR_EDIT');
        }
        
        
        if ($canDo->get('core.edit.state'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::publish('courses.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('courses.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            
            JToolBarHelper::divider();
            JToolBarHelper::archiveList('courses.archive');
            JToolBarHelper::checkin('courses.checkin');
        }
        
        if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
        {
            JToolBarHelper::deleteList('', 'courses.delete', 'JTOOLBAR_EMPTY_TRASH');
            JToolBarHelper::divider();
        } else if ($canDo->get('core.edit.state'))
        {
            JToolBarHelper::trash('courses.trash', 'JTOOLBAR_TRASH');
            JToolBarHelper::divider();
        }
        if ($canDo->get('core.admin'))
        {
            JToolBarHelper::preferences('com_courses');
            JToolBarHelper::divider();
        }
        JToolBarHelper::help('', '', JText::_('COM_COURSES_COURSES_HELP_LINK'));
    }
}