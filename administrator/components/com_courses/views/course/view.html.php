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
 * 
 * View to edit a contact
 * 
 * @package Joomla.Administrator
 * @subpackage com_courses
 * 
 */

class CoursesViewCourse extends JView
{
    protected $form;
    protected $item;
    protected $state;
    
    /**
     * Display the view.
     */
    
    public function display($tpl = null)
    {
        // Initialize variables.
        $this->form     = $this->get('Form');
        $this->item     = $this->get('Item');
        $this->state     = $this->get('State');
        
        // Check errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }
        $this->addToolbar();
        parent::display($tpl);
    }
    
    protected function addToolbar()
    {
        JRequest::setVar('hidemainmenu', true);
        
        $user = JFactory::getUser();
        $isNew = ($this->item->id == 0);
        $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        $canDo = CoursesHelper::getActions($this->state->get('filter.category_id'), $this->item->id);
        
        JToolBarHelper::title(JText::_('COM_COURSES_MANAGER_COURSE'), 'newfeeds.png');
        
        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || (count($user->getAuthorisedCategories('com_courses', 'core.create')))))
        {
            JToolBarHelper::apply('course.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('course.save', 'JTOOLBAR_SAVE');
        }
        if (!checkedOut && (count($user->getAuthorisedCategories('com_courses', 'core.create'))))
        {
            JToolBarHelper::custom('course.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }
        
        // If an existing item, can save to a copy.
        if (!$isNew & (count($user->getAuthorisedCategories('com_courses', 'core.create')) > 0))
        {
            JToolBarHelper::custom('course.save2copy', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }
        if (empty($this->item->id))
        {
            JToolBarHelper::cancel('course.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolBarHelper::cancel('course.cancel', 'JTOOLBAR_CLOSE');
        }
        JToolBarHelper::divider();
        JToolBarHelper::help('', '', JText::_('COM_COURSES_COURSE_HELP_LINK'));
    }
}