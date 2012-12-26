<?php

// no direct access
defined('_JEXEC') or die;

class CoursesController extends JController
{
    function display($cachable = false, $urlparams = false)
    {
    JLoader::register('CoursesHelper', JPATH_COMPONENT.'/helpers/courses.php');
    

    // Load the submenu
    CoursesHelper::addSubmenu(JRequest::getCmd('view', 'courses'));

    $view    = JRequest::getCmd('view', 'courses');
    $layout  = JRequest::getCmd('layout', 'default');
    $id      = JRequest::getInt('id');


    // Check for edit form.
    if ($view == 'courses' && $layout =='edit' && !$this->checkEditId('com_courses.edit.course', $id))
        {
            // Somehow the person just went to the form - we don't allow that
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_( 'index.php?option=com_courses&view=courses', false));
            return false;
        }
        parent::display();
        
        return $this;

}
}

