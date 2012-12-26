<?php
defined ('_JEXEC') or die;
jimport ('joomla.application.component.view');

class CoursesViewCourses extends JView
{
    protected $item;
    
    function display ($tpl = null)
    {
        $this->items = $this->get('Items'); 
        parent::display($tpl);
    }
}