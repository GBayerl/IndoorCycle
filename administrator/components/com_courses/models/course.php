<?php
/**
 *
 * @package Joomla.Administrator
 * @subpackage com_courses
 * @copyright (c) 2012, Guenther Bayerl. All rights reserved
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Course model.
 * 
 * @package Joomla.Administrator
 * @subpackage com_courses
 * @since 2.5
 * 
 */

class CoursesModelCourse extends JModelAdmin
{
    /**
     * @var string The prefix to use with controller messages.
     */
    
    protected $text_prefix = 'COM_COURSES';
    
    /**
     * 
     * Method to test whether a record can be deleted.
     * 
     * @param object A record object.
     * @return Boolean True if allowed to delete the record. Defualts to the permission set in the component.
     * 
     */
    
    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            if ($record->published != -2)
            {
                return ;
            }
            $user = JFactory::getUser();
            
            if ($record->catid)
            {
                return $user->authorise('core.delete', 'com_courses.category'.(int) $record->catid);
            } else {
                return parent::canDelete ($record);
            }
        }
    }
    
    
    protected function canEditState ($record)
    {
        $user = JFactory::getUser();
        
        if (!empty($record->catid))
        {
            return $user->authorise('core.edit.state', 'com_courses.category.'.(int) $record->catid);
        } else {
            return parent::canEditState($record);
        }
    }
    
    public function getTable($type = 'course', $prefix = 'CoursesTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }
    
    public function getForm($data = array(), $loadData = true)
    {
        // Initialise variables.
        $app = JFactory::getApplication();
        
        // Get the form.
        $form = $this->loadForm('com_courses.course', 'course', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }
        
        // Determine correct permissions to check.
        if ($this->getState('course.id'))
        {
            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');
        } else {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }
        
        // Modify the form based on access controls
        if (!$this->canEditState((object) $data))
        {
            // Disable fields for display.
            $form->setFieldAttribute('published', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');
            
            // Disable fields while saving.
            // The controller as already verified this is a record you can edit.
            $form->setFieldAttribute('published', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
        }
        return $form;        
        
     }

        
        /**
         * Method to get the data that should be injected in the form.
         * 
         * @return mixed The data for the form.
         */
        
        protected function loadFormData()
        {
            // Check the session for previously entered form data.
            $data = JFactory::getApplication()->getUserState('com_courses.edit.course.data', array());
            
            if (empty($data))
            {
                $data = $this->getItem();
                
                // Prime some default values.
                if ($this->getState('course.id') == 0)
                {
                    $app = JFactory::getApplication();
                    $data->set('catid', JRequest::getInt('catid', $app->getUserState('com_courses.courses.filter.category_id')));
                }
            }
            return $data;
        }
        
        protected function prepareTable(&$table)
        {
            $table->alias = JApplication::stringURLSafe($table->alias);
            if (empty($table->alias))
            {
                $table->alias = JApplication::stringURLSafe($table->coursename);
            }
        }
    
}
