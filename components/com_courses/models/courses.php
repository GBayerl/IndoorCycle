<?php
defined ('_JEXEC') or die;
jimport ('joomla.application.component.modellist');

class CoursesModelCourses extends JModelList
{
    public $freebikes;
    
    protected function getListQuery()
    {
      
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->from('#__courses AS a');
        $query->leftJoin ('#__courses_level AS c ON a.levelid = c.level_id ');
        $query->leftJoin ('#__courses_map AS b ON a.course_id = b.course_id ');
        $query->select('a.course_id, a.coursename, a.description, c.levelname, a.trainer, a.coursedate, a.coursetime, a.duration, (a.bikes - COUNT(b.user_id)) AS \'free\'');
        $query->group('a.course_id');
        echo $query->dump();
       return $query;
        
    }   
}