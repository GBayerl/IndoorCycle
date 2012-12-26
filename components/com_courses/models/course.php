<?php
defined ('_JEXEC') or die;
jimport ('joomla.application.component.modelitem');

class CoursesModelCourse extends JModelItem
{
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        $data = $app->getUserState('com_courses.edit.course.data', array());
        
        if (empty($data))
        {
            $data = $this->getItem();
        }
        return $data;
    }
    
    public function getForm($data = array(), $loadData = true)
    {
        echo "Get Form <br />";
        $options = array('control' => 'jform', 'load_data' => $loadData);
        $form = $this->loadForm('courses', 'course', $options);
        echo "Form start! <br />";
        if (empty($form))
        {
            echo "Form empty! <br />";
            return false;
        }
        echo "Form not empty! <br />";
        return $form;
        echo $form;
    }
    
    public function getItem($id=null)
        
    {
        
    $result = null;
        $app = JFactory::getApplication();
        $requested_id = $app->get('input')->get('id', 0, 'int');
        if ($requested_id > 0)
        {
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->from('#__courses');
            $query->select('*');
            $query->where('course_id = ' . $requested_id);
            $db->setQuery($query);
            $result = $db->loadObject();
        }
        return $result;
    }
}