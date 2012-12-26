<?php
defined('_JEXEC') or die;

class CoursesTableSubscribe extends JTable
{
    public $user_id;
    public $course_id;
    public $descriptioncontactnumber;
    public $created;
    
    public function __construct($db)
    {
        parent::__construct('#__courses_map', 'user_id', $db);
    }
}