<?php
defined ('_JEXEC') or die;
jimport ('joomla.application.component.view');

class CoursesViewCourse extends JView
{
    protected $item;
    //protected $form;
    protected $params;
    
    function display($tpl = null)
    {
        $this->item = $this->get('Item');
        // $this->form = $this->get('Form');
        parent::display($tpl);
    }
}