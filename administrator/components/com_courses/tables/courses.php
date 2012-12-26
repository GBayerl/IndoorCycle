<?php
defined('_JEXEC') or die;

class CoursesTableCourses extends JTable
{
    public $course_id;
    public $coursename;
    public $description;
    public $levelid;
    public $trainer;
    public $coursedate;
    public $coursetime;
    public $duration;
    public $bikes;
    public $createdby;
    public $lastmodified;
    
    public function __construct($db)
    {
        parent::__construct('#__courses', 'cousre_id', $db);
    }
}