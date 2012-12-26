<?php
defined ('_JEXEC') or die;
jimport ('joomla.application.component.modellist');

class CoursesModelSubscriber extends JModelList
{
    public function getListQuery($id=null)
    {
        echo "start here";
        $app = JFactory::getApplication();
        $requested_id = $app->get('input')->get('id', 0, 'int');
        if ($requested_id > 0)
        {
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->from('#__courses_map as b');
            $query->select('a.id as userid, a.name as username, b.created as subscriptiontime, b.contactnumber as contactnumber, c.coursename as coursename');
            $query->leftjoin('#__users as a ON a.id = b.user_id');
            $query->leftjoin('#__courses as c ON c.course_id = b.course_id');
            $query->where('b.course_id = \'' . $requested_id . '\'');
            $query->order('b.created');
            return $query;
            
         }

        
    }
}