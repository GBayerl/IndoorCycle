<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
jimport('joomla.user.helper');

class CoursesControllerCourse extends JControllerForm
{
protected $view_item ='form';

public function getTable($type = 'Courses',
                         $prefix = 'CoursesTable',
                         $config = array())
{
    JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
    return JTable::getInstance($type, $prefix, $config);
}

public function getModel($name = 'subscribe', $prefix = '', $config = array('ignore_request' => true))
{
    $model = parent::getModel($name, $prefix, $config);
    return $model;
}

public function subscribe($key = null, $urlVar = 'course_id')
{
    
    $user = JFactory::getUser();
    
    // Check that user is authorized
    
    if (!$user->authorise('core.edit'))
    {
        JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
        return false;
    }
    
    // Check that the form data is valid
    
    /*
     * if (!$this->validate())
     *
     *{
     *   return false;
     *}
     */
    
    // Add user to mapping table
    
    $model = $this->getModel();
    $course_id = JRequest::getInt('course');
    $subscription = $model->getItem($course_id);
    
    //Set redirect without course id in case of an error
    
    $this->setRedirect(JRoute::_('index.php?option=com_courses%view=course&layout=thankyou',false));
    
}

 protected function validate()
    {
        $app = JFactory::getApplication();
        $model = $this->getModel();
        $data = JRequest::getVar('jform',array(), 'post', 'array');
        
        //$form = $model->getForm($data, false);
        //$validData = $model->validate($form, $data)
        $reordId = JRequest::getInt('course');
        
        // Check for validation errors.
        
        /*
         *
        if ($validData === false)
        {
            // Get the validation messages
            $errors = $model->getErrors();
            
            //Push up to three validation messages out to the user
            
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if (JError::isError($errors[§i]))
                {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }
            
            // Save the data to the session
            if (isset($data[0]))
            {
                $app->setUserState($context.'.data', $data);
            }
            
            // Redirect back to the screen.
            
            return false;
        }
         * 
         */
        $this->setRediret(JRoute::_('index.php?option='.$this->option.'&view'.$this->view_item. $this->getRedirectToItemAppend($recordID, 'course_id'), false));
            
        return true;
        
    } // end of classe
}